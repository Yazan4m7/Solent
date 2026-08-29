import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';

const root = process.cwd();
const read = rel => fs.readFileSync(path.join(root, rel), 'utf8');
const walk = dir => fs.readdirSync(path.join(root, dir), { withFileTypes: true }).flatMap(entry => {
  const rel = path.join(dir, entry.name);
  return entry.isDirectory() ? walk(rel) : [rel];
});

test('stock and financing keep their CSS isolated from the Solent global stylesheet', () => {
  assert.ok(fs.existsSync(path.join(root, 'public/css/stock.css')));
  assert.ok(fs.existsSync(path.join(root, 'public/css/financing.css')));

  const stockCss = read('public/css/stock.css');
  const financeCss = read('public/css/financing.css');

  const cssWithoutComments = stockCss.replace(/\/\*[\s\S]*?\*\//g, '');
  const selectorGroups = [...cssWithoutComments.matchAll(/([^{}]+)\{/g)]
    .map(match => match[1].trim())
    .filter(group => !group.startsWith('@'));
  const unscoped = selectorGroups
    .flatMap(group => group.split(',').map(selector => selector.trim()))
    .filter(Boolean)
    .filter(selector => !selector.includes('.stock-'));

  assert.deepEqual(unscoped, [], `Unscoped stock selectors: ${unscoped.slice(0, 10).join(', ')}`);

  // Financing may use Bootstrap utility classes in markup, but its custom stylesheet must scope custom rules.
  assert.match(financeCss, /\.financing-module|\.finance-/);
});

test('all state-changing module forms have CSRF protection', () => {
  const roots = [
    'app/Modules/Stock/Resources/views',
    'app/Modules/Financing/Resources/views',
  ];

  const failures = [];
  for (const base of roots) {
    for (const rel of walk(base).filter(f => f.endsWith('.blade.php'))) {
      const source = read(rel);
      const forms = [...source.matchAll(/<form\b[\s\S]*?<\/form>/gi)].map(m => m[0]);
      for (const form of forms) {
        const method = (form.match(/\bmethod=["']([^"']+)/i)?.[1] ?? 'GET').toUpperCase();
        if (method !== 'GET' && !/@csrf|csrf_field\s*\(/.test(form)) {
          failures.push(rel);
        }
      }
    }
  }
  assert.deepEqual([...new Set(failures)], []);
});

test('module pages do not load remote scripts or styles', () => {
  const files = [
    ...walk('app/Modules/Stock/Resources/views'),
    ...walk('app/Modules/Financing/Resources/views'),
  ].filter(f => f.endsWith('.blade.php'));

  const failures = [];
  for (const rel of files) {
    const source = read(rel);
    if (/<(?:script|link)[^>]+(?:src|href)=["']https?:\/\//i.test(source)) failures.push(rel);
  }
  assert.deepEqual(failures, []);
});

test('financing uses the css stack actually rendered by layouts.app', () => {
  const layout = read('resources/views/layouts/app.blade.php');
  const finance = read('app/Modules/Financing/Resources/views/financing/_layout.blade.php');
  assert.match(layout, /@stack\(['"]css['"]\)/);
  assert.match(finance, /@push\(['"]css['"]\)/);
  assert.doesNotMatch(finance, /@push\(['"]styles['"]\)/);
});

test('stock purchase line javascript is local and no inline remote dependency is introduced', () => {
  const purchase = read('app/Modules/Stock/Resources/views/stock/purchases/create.blade.php');
  assert.match(purchase, /asset\(['"]js\/stock\.js['"]\)/);
  assert.doesNotMatch(purchase, /https?:\/\//);
});
