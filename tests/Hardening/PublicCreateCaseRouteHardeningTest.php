<?php

namespace Tests\Hardening;

use Tests\TestCase;

class PublicCreateCaseRouteHardeningTest extends TestCase
{
    public function test_create_case_page_is_not_publicly_accessible_without_authentication(): void
    {
        $response = $this->get('/new-case');

        $this->assertContains(
            $response->getStatusCode(),
            [302, 401, 403],
            'GET /new-case must be protected by authentication and CreateCase authorization.'
        );
    }
}
