<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{


    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function currentDomainContext(): array
    {
        if (! app()->bound('app.domain_context')) {
            return [];
        }

        $context = app('app.domain_context');

        return is_array($context) ? $context : [];
    }

    protected function currentCurrencyCode(): string
    {
        $context = $this->currentDomainContext();
        $currencyCode = strtoupper(trim((string) ($context['currency_code'] ?? 'JOD')));

        return $currencyCode !== '' ? $currencyCode : 'JOD';
    }
}
