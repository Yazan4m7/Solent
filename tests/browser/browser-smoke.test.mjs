import test from 'node:test';
import assert from 'node:assert/strict';

const baseUrl = (process.env.SOLENT_E2E_BASE_URL || '').replace(/\/+$/, '');
const username = process.env.SOLENT_E2E_USERNAME || '';
const password = process.env.SOLENT_E2E_PASSWORD || '';
const baseSkip = baseUrl ? false : 'Set SOLENT_E2E_BASE_URL to run browser tests.';

async function loadPlaywright(t) {
    try {
        return await import('playwright');
    } catch (error) {
        t.skip('Playwright is not installed. Run: npm install --no-save playwright@1.55.0');
        return null;
    }
}

async function assertNoHorizontalOverflow(page, tolerance = 4) {
    const overflow = await page.evaluate(() => Math.max(
        0,
        document.documentElement.scrollWidth - document.documentElement.clientWidth,
        document.body ? document.body.scrollWidth - document.body.clientWidth : 0
    ));
    assert.ok(overflow <= tolerance, `Horizontal overflow detected: ${overflow}px`);
}

test('login page has no runtime exception, works on mobile width and toggles password visibility', { skip: baseSkip }, async (t) => {
    const playwright = await loadPlaywright(t);
    if (!playwright) return;

    const browser = await playwright.chromium.launch({ headless: true });
    const page = await browser.newPage({ viewport: { width: 390, height: 844 } });
    const pageErrors = [];
    page.on('pageerror', (error) => pageErrors.push(error.message));

    try {
        const response = await page.goto(`${baseUrl}/login`, { waitUntil: 'domcontentloaded' });
        assert.ok(response && response.status() < 500, `Login returned ${response?.status()}`);
        await assertNoHorizontalOverflow(page);

        const passwordInput = page.locator('input[name="password"]').first();
        assert.equal(await passwordInput.count(), 1, 'Password input missing');

        const toggle = page.locator('.eye-toggle').first();
        if (await toggle.count()) {
            assert.equal(await passwordInput.getAttribute('type'), 'password');
            await toggle.click();
            assert.equal(await passwordInput.getAttribute('type'), 'text');
            await toggle.click();
            assert.equal(await passwordInput.getAttribute('type'), 'password');
        }

        assert.deepEqual(pageErrors, [], `Browser page errors: ${pageErrors.join(' | ')}`);
    } finally {
        await browser.close();
    }
});

test('authenticated critical-page sweep catches 5xx, JS exceptions and responsive overflow', { skip: baseSkip }, async (t) => {
    if (!username || !password) {
        t.skip('Set SOLENT_E2E_USERNAME and SOLENT_E2E_PASSWORD for authenticated browser coverage.');
        return;
    }

    const playwright = await loadPlaywright(t);
    if (!playwright) return;

    const browser = await playwright.chromium.launch({ headless: true });
    const page = await browser.newPage({ viewport: { width: 1366, height: 768 } });

    try {
        await page.goto(`${baseUrl}/login`, { waitUntil: 'domcontentloaded' });
        const identity = page.locator('input[name="username"], input[name="email"]').first();
        assert.equal(await identity.count(), 1, 'Username/email input missing');
        await identity.fill(username);
        await page.locator('input[name="password"]').first().fill(password);
        await page.locator('button[type="submit"], input[type="submit"]').first().click();
        await page.waitForLoadState('domcontentloaded');
        assert.ok(!page.url().includes('/login'), 'Authentication did not leave the login page');

        const paths = (process.env.SOLENT_E2E_PATHS || '/home,/operations-dashboard,/invoices,/payments/index')
            .split(',')
            .map((value) => value.trim())
            .filter(Boolean);

        for (const path of paths) {
            const errors = [];
            const listener = (error) => errors.push(error.message);
            page.on('pageerror', listener);

            const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'domcontentloaded' });
            assert.ok(response && response.status() < 500, `${path} returned ${response?.status()}`);
            await assertNoHorizontalOverflow(page, 8);
            assert.deepEqual(errors, [], `${path} page errors: ${errors.join(' | ')}`);

            page.off('pageerror', listener);
        }
    } finally {
        await browser.close();
    }
});
