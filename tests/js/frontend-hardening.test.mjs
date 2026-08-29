import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '../..');
const read = (path) => readFileSync(resolve(root, path), 'utf8');

test('application shell does not include the same stylesheet twice', () => {
    const source = read('resources/views/layouts/app.blade.php');
    const hrefs = [...source.matchAll(/<link\b[^>]*\bhref="([^"]+)"[^>]*>/gi)].map((match) => match[1].trim());
    const duplicates = [...new Set(hrefs.filter((href, index) => hrefs.indexOf(href) !== index))];

    assert.deepEqual(duplicates, [], `Duplicate stylesheets found: ${duplicates.join(', ')}`);
});

test('subresource integrity attributes use a syntactically valid hash token', () => {
    const source = read('resources/views/layouts/app.blade.php');
    const integrities = [...source.matchAll(/\bintegrity="([^"]+)"/gi)].map((match) => match[1]);

    for (const integrity of integrities) {
        const tokens = integrity.trim().split(/\s+/);
        assert.ok(tokens.length > 0);
        for (const token of tokens) {
            assert.match(token, /^(sha256|sha384|sha512)-[A-Za-z0-9+/=]+$/, `Malformed integrity token: ${token}`);
        }
    }
});
