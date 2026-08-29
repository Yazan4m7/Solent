import test from 'node:test';
import assert from 'node:assert/strict';

const baseUrl = (process.env.SOLENT_TEST_BASE_URL || '').replace(/\/+$/, '');
const liveOnly = baseUrl ? false : 'Set SOLENT_TEST_BASE_URL to run live HTTP integration tests.';

async function request(path, options = {}) {
    return fetch(`${baseUrl}${path}`, {
        redirect: 'manual',
        ...options,
    });
}

test('login endpoint renders without a server error and exposes a CSRF token', { skip: liveOnly }, async () => {
    const response = await request('/login', { redirect: 'follow' });
    const html = await response.text();

    assert.ok(response.status >= 200 && response.status < 400, `Unexpected /login status ${response.status}`);
    assert.match(html, /csrf-token|name="_token"/i);
    assert.doesNotMatch(html, /Fatal error|Stack trace|Whoops, looks like something went wrong/i);
});

test('critical private pages reject anonymous users instead of rendering application data', { skip: liveOnly }, async () => {
    const paths = ['/home', '/operations-dashboard', '/invoices', '/payments/index', '/new-case'];

    for (const path of paths) {
        const response = await request(path);
        assert.ok(
            [301, 302, 303, 307, 308, 401, 403].includes(response.status),
            `${path} returned ${response.status} to an anonymous request`
        );
    }
});

test('unknown route returns a client error rather than a server error', { skip: liveOnly }, async () => {
    const response = await request('/__solent_test_missing_route__');
    assert.ok(response.status >= 400 && response.status < 500, `Unexpected missing-route status ${response.status}`);
});

test('hostile search input never produces a 5xx response', { skip: liveOnly }, async () => {
    const payload = encodeURIComponent(`'\"><script>alert(1)</script>\\%00`);
    const response = await request(`/search?searchText=${payload}`);

    assert.ok(response.status < 500, `Hostile search request produced ${response.status}`);
});
