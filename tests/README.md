# Solent regression and hardening tests

This test layer covers several bug classes instead of relying only on PHPUnit markup checks.

## 1. Backend baseline

Run the normal Laravel suite:

```bash
php vendor/bin/phpunit
```

New baseline coverage includes tenant-context normalization, demo isolation, demo read-only enforcement, route/controller integration contracts, CSRF middleware placement, financial model relationships, and SoftDeletes contracts.

## 2. Hardening suite

The hardening directory is intentionally **not** part of the default `phpunit.xml` suites. It is designed to expose existing legacy defects such as duplicate/shadowed routes and permission middleware that crashes when its cache is missing.

```bash
php vendor/bin/phpunit tests/Hardening
```

A hardening failure means the test found a defect or an unsafe legacy assumption; it is not silently allow-listed.

## 3. Frontend source contracts (Node, no extra dependency)

```bash
npm run test:frontend
```

Strict frontend hardening checks:

```bash
npm run test:frontend:hardening
```

The strict suite checks duplicate stylesheet imports and malformed Subresource Integrity values in the application shell.

## 4. Live HTTP integration tests

These test the deployed Laravel/web-server integration, anonymous authorization boundaries, exception leakage and hostile query input.

PowerShell:

```powershell
$env:SOLENT_TEST_BASE_URL='https://your-solent-host.example'
npm run test:http
```

Bash:

```bash
SOLENT_TEST_BASE_URL=https://your-solent-host.example npm run test:http
```

## 5. Real browser tests

The browser suite uses Chromium through Playwright and checks actual JavaScript execution, mobile overflow, password UX, authentication, critical pages and page-level JS exceptions.

Install Playwright without changing the project lockfile:

```bash
npm install --no-save playwright@1.55.0
npx playwright install chromium
```

PowerShell:

```powershell
$env:SOLENT_E2E_BASE_URL='https://your-solent-host.example'
$env:SOLENT_E2E_USERNAME='test-user'
$env:SOLENT_E2E_PASSWORD='test-password'
npm run test:browser
```

Use a dedicated test/demo tenant and test account. Do not run destructive scenarios against production data.

## Coverage philosophy

No finite suite can prove that every possible bug is absent. The approach here is to cover broad bug classes: backend contracts, middleware/security boundaries, tenancy isolation, data-model integrations, static frontend regressions, live HTTP behavior, responsive rendering, and real-browser JavaScript errors. Add a regression test whenever a new bug is found so the same defect cannot return unnoticed.
