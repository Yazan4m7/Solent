import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '../..');
const read = (path) => readFileSync(resolve(root, path), 'utf8');

test('application shell keeps CSRF, locale direction, flash and extension hooks', () => {
    const source = read('resources/views/layouts/app.blade.php');

    assert.match(source, /<meta\s+name="csrf-token"\s+content="\{\{\s*csrf_token\(\)\s*\}\}"/);
    assert.match(source, /<html[^>]+dir="\{\{\s*trans\('ui\.direction'\)\s*\}\}"/);
    assert.ok(source.includes("session()->has('error')"));
    assert.ok(source.includes("session()->has('success')"));
    assert.ok(source.includes("@stack('css')"));
});

test('login template keeps CSRF, password UX and responsive RTL contracts', () => {
    const source = read('resources/views/auth/login.blade.php');

    assert.ok(source.includes('@csrf'), 'Login form must include CSRF protection.');
    assert.match(source, /type="password"/);
    assert.ok(source.includes('eye-toggle'));
    assert.ok(source.includes('html[dir="rtl"]'));
    assert.ok(source.includes('@media (max-width: 430px)'));
    assert.match(source, /type="submit"/);
});

test('sidebar keeps mobile, RTL and permission-aware navigation behavior', () => {
    const source = read('resources/views/layouts/navbars/leftsidebar.blade.php');

    assert.ok(source.includes('solent-mobile-sidebar-toggle'));
    assert.ok(source.includes('html[dir="rtl"]'));
    assert.ok(source.includes('$canAccessSidebar'));
    assert.ok(source.includes("Cache::get('user' . Auth()->user()->id)"));
    assert.ok(source.includes("route('admin-dashboard-v2')"));
    assert.ok(source.includes("route('invoices-index')"));
});

test('create-case frontend keeps row-scoped job controls and required form field names', () => {
    const source = read('app/Modules/Cases/Resources/views/cases/create.blade.php');

    for (const field of ['patient_name', 'doctor', 'delivery_date']) {
        assert.ok(source.includes(`name="${field}"`), `Missing create-case field ${field}`);
    }

    assert.ok(source.includes('data-repeater-list="repeat"'));
    assert.ok(source.includes('function getJobBlock(element)'));
    assert.ok(source.includes('function getSelectedUnitsForJob(row)'));
    assert.ok(source.includes("getSelectInJob(jobRow, 'jobType')"));
    assert.ok(source.includes("getSelectInJob(jobRow, 'material_id')"));
});

test('quick navigation uses named routes and permission checks instead of hard-coded URLs', () => {
    const source = read('resources/views/layouts/navbars/quicknav.blade.php');

    assert.ok(source.includes('$canUseQuickNav'));
    assert.ok(source.includes("'route' => 'home'"));
    assert.ok(source.includes("'route' => 'cases-index'"));
    assert.match(source, /href="\{\{\s*route\(\$quickNavItem\['route'\]\)\s*\}\}"/);
});
