<?php

namespace Tests\Hardening;

use Tests\TestCase;

/**
 * Deliberately strict regression checks. These are kept outside the default
 * PHPUnit suites so they can expose legacy defects without blocking normal work.
 * Run with: php vendor/bin/phpunit tests/Hardening
 */
class RouteCollisionHardeningTest extends TestCase
{
    public function test_routes_web_php_has_no_duplicate_literal_method_and_uri_pairs(): void
    {
        $source = file_get_contents(base_path('routes/web.php'));
        $source = preg_replace('~/\*.*?\*/~s', '', $source) ?? $source;
        $source = preg_replace('~^\s*//.*$~m', '', $source) ?? $source;

        preg_match_all(
            '~Route::(get|post|put|patch|delete|options)\(\s*[\'\"]([^\'\"]+)[\'\"]~i',
            $source,
            $matches,
            PREG_SET_ORDER
        );

        $seen = [];
        $duplicates = [];

        foreach ($matches as $match) {
            $key = strtoupper($match[1]) . ' ' . ltrim($match[2], '/');
            if (isset($seen[$key])) {
                $duplicates[] = $key;
            }
            $seen[$key] = true;
        }

        $this->assertSame([], array_values(array_unique($duplicates)), 'Duplicate routes can shadow middleware or controller changes.');
    }
}
