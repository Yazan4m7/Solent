<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\Tenancy\TenantDatabaseManager;
use App\Support\Tenancy\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ApplyDomainContext
{
    public function handle(Request $request, Closure $next)
    {
        $selectedOption = $this->resolveSelectionOption((string) $request->query('select_country_domain', ''));
        if ($selectedOption !== null) {
            return $this->redirectWithSavedSelection($request, $selectedOption);
        }

        $savedSelectionRedirect = $this->resolveSavedSelectionRedirect($request);
        if ($savedSelectionRedirect !== null) {
            return redirect()->away($savedSelectionRedirect);
        }

        $tenantContext = app(TenantResolver::class)->resolve($request);
        if (!$tenantContext->isResolved()) {
            return $this->renderDomainSelectionPage($request, (string) ($tenantContext->reason ?? 'unknown'));
        }

        if (!$tenantContext->isActive()) {
            return response()->view('errors.tenant-status', [
                'tenantContext' => $tenantContext,
                'requestedHost' => $this->normalizeHost($request->getHost()),
            ], 423);
        }

        app(TenantDatabaseManager::class)->connect($tenantContext);

        try {
            $this->verifyDatabaseConnection();
        } catch (\Throwable $exception) {
            report($exception);

            return $this->renderDomainSelectionPage($request, 'database_unavailable', $tenantContext->domainContext());
        }

        app()->instance('app.tenant_context', $tenantContext);
        $this->shareContext($tenantContext->domainContext());

        return $next($request);
    }

    private function resolveDomainContext(?string $host): array
    {
        $defaultContext = (array) config('domain_context.default', []);
        $hostsMap = (array) config('domain_context.hosts', []);
        $normalizedHost = $this->normalizeHost($host);

        if ($normalizedHost === '') {
            return [
                'valid' => false,
                'reason' => 'missing_host',
            ];
        }

        if (!array_key_exists($normalizedHost, $hostsMap)) {
            return [
                'valid' => false,
                'reason' => 'unmapped_host',
            ];
        }

        $context = array_merge($defaultContext, (array) $hostsMap[$normalizedHost], ['matched' => true]);

        if (!$this->isContextComplete($context)) {
            return [
                'valid' => false,
                'reason' => 'invalid_context',
            ];
        }

        return [
            'valid' => true,
            'context' => $context,
        ];
    }

    private function applyDatabaseConnection(?string $databaseName): void
    {
        if (!is_string($databaseName) || $databaseName === '') {
            return;
        }

        $connectionNames = array_unique(array_filter([
            config('database.default'),
            'mysql',
        ]));

        foreach ($connectionNames as $connectionName) {
            $connectionConfig = config('database.connections.' . $connectionName);
            if (!is_array($connectionConfig) || !array_key_exists('database', $connectionConfig)) {
                continue;
            }

            if (($connectionConfig['database'] ?? null) === $databaseName) {
                continue;
            }

            config()->set('database.connections.' . $connectionName . '.database', $databaseName);
            DB::purge($connectionName);
        }
    }

    private function verifyDatabaseConnection(): void
    {
        $connectionName = (string) config('database.default', 'mysql');
        DB::connection($connectionName)->getPdo();
    }

    private function isContextComplete(array $context): bool
    {
        $requiredFields = ['country_code', 'country_name', 'database'];

        foreach ($requiredFields as $field) {
            $value = $context[$field] ?? null;
            if (!is_string($value) || trim($value) === '') {
                return false;
            }
        }

        return true;
    }

    private function renderDomainSelectionPage(Request $request, string $reason, array $context = [])
    {
        $statusCode = $reason === 'database_unavailable' ? 503 : 421;

        return response()->view('errors.domain-selection', [
            'reason' => $reason,
            'requestedHost' => $this->normalizeHost($request->getHost()),
            'domainOptions' => $this->domainOptions($request),
            'domainContext' => $context,
        ], $statusCode);
    }

    private function domainOptions(Request $request): array
    {
        $options = $this->configuredDomainOptions();
        $preparedOptions = [];

        foreach ($options as $option) {
            $preparedOptions[] = array_merge($option, [
                'selection_url' => $request->fullUrlWithQuery(['select_country_domain' => $option['host']]),
            ]);
        }

        return $preparedOptions;
    }

    private function configuredDomainOptions(): array
    {
        $configuredOptions = (array) config('domain_context.selection_domains', []);
        $preparedOptions = [];

        foreach ($configuredOptions as $option) {
            if (!is_array($option)) {
                continue;
            }

            $host = $this->normalizeHost($option['host'] ?? '');
            $url = (string) ($option['url'] ?? '');
            if ($host === '' || $url === '') {
                continue;
            }

            $preparedOptions[] = [
                'host' => $host,
                'url' => $url,
                'country_code' => strtoupper((string) ($option['country_code'] ?? '')),
                'country_name' => (string) ($option['country_name'] ?? $host),
            ];
        }

        if (count($preparedOptions) > 0) {
            return $preparedOptions;
        }

        $hostsMap = (array) config('domain_context.hosts', []);
        foreach ($hostsMap as $host => $context) {
            if (!is_array($context)) {
                continue;
            }

            $normalizedHost = $this->normalizeHost((string) $host);
            if ($normalizedHost === '') {
                continue;
            }

            $preparedOptions[] = [
                'host' => $normalizedHost,
                'url' => 'https://' . $normalizedHost,
                'country_code' => strtoupper((string) ($context['country_code'] ?? '')),
                'country_name' => (string) ($context['country_name'] ?? $normalizedHost),
            ];
        }

        return $preparedOptions;
    }

    private function resolveSelectionOption(string $selectedValue): ?array
    {
        $token = strtolower(trim($selectedValue));
        if ($token === '') {
            return null;
        }

        foreach ($this->configuredDomainOptions() as $option) {
            if ($token === strtolower($option['host'])) {
                return $option;
            }

            if ($token === strtolower((string) ($option['country_code'] ?? ''))) {
                return $option;
            }
        }

        return null;
    }

    private function resolveSavedSelectionRedirect(Request $request): ?string
    {
        $selectionHost = $this->normalizeHost((string) config('domain_context.selection_host', 'lab.alsolentco.com'));
        $currentHost = $this->normalizeHost($request->getHost());
        if ($selectionHost === '' || $currentHost !== $selectionHost) {
            return null;
        }

        $cookieName = (string) config('domain_context.selection_cookie.name', 'lab_country_domain');
        $cookieValue = (string) $request->cookie($cookieName, '');
        $selectedOption = $this->resolveSelectionOption($cookieValue);

        if ($selectedOption === null) {
            return null;
        }

        return (string) ($selectedOption['url'] ?? null);
    }

    private function redirectWithSavedSelection(Request $request, array $option)
    {
        $cookieName = (string) config('domain_context.selection_cookie.name', 'lab_country_domain');
        $cookieMinutes = (int) config('domain_context.selection_cookie.minutes', 525600);
        $cookieDomain = (string) config('domain_context.selection_cookie.domain', '.lab.alsolentco.com');
        $cookieValue = (string) ($option['host'] ?? '');
        $targetUrl = (string) ($option['url'] ?? '');

        if ($cookieValue === '' || $targetUrl === '') {
            return $this->renderDomainSelectionPage($request, 'invalid_context');
        }

        return redirect()
            ->away($targetUrl)
            ->withCookie(cookie(
                $cookieName,
                $cookieValue,
                $cookieMinutes,
                '/',
                $cookieDomain,
                $request->isSecure(),
                true,
                false,
                'Lax'
            ));
    }

    private function isLocalRuntime(Request $request): bool
    {
        if (App::environment(['local', 'development', 'testing'])) {
            return true;
        }

        $host = $this->normalizeHost($request->getHost());
        if ($host === '') {
            return true;
        }

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return true;
        }

        return (bool) preg_match('/\.(test|testing|localhost|local|lan|internal|loc)$/', $host);
    }

    private function localContext(?string $host): array
    {
        $defaultConnectionName = (string) config('database.default', 'mysql');
        $activeDatabase = config('database.connections.' . $defaultConnectionName . '.database');
        if (!is_string($activeDatabase) || trim($activeDatabase) === '') {
            $activeDatabase = (string) config('database.connections.mysql.database', '');
        }

        return array_merge((array) config('domain_context.default', []), [
            'matched' => false,
            'source' => 'local_bypass',
            'host' => $this->normalizeHost($host),
            'database' => (string) $activeDatabase,
        ]);
    }

    private function normalizeHost(?string $host): string
    {
        $normalized = strtolower(trim((string) $host));

        if (strpos($normalized, 'www.') === 0) {
            return substr($normalized, 4);
        }

        return $normalized;
    }

    private function shareContext(array $context): void
    {
        $normalizedContext = $this->normalizeContext($context);

        app()->instance('app.domain_context', $normalizedContext);
        View::share('domainContext', $normalizedContext);
        View::share('currencyContext', $this->currencyContext($normalizedContext));
    }

    private function normalizeContext(array $context): array
    {
        $defaultContext = (array) config('domain_context.default', []);
        $normalized = array_merge($defaultContext, $context);

        $currencyCode = strtoupper(trim((string) ($normalized['currency_code'] ?? 'JOD')));
        $normalized['currency_code'] = $currencyCode !== '' ? $currencyCode : 'JOD';

        $currencyDisplay = trim((string) ($normalized['currency_display'] ?? $normalized['currency_code']));
        $normalized['currency_display'] = $currencyDisplay !== '' ? $currencyDisplay : $normalized['currency_code'];

        $currencySymbol = trim((string) ($normalized['currency_symbol'] ?? $normalized['currency_display']));
        $normalized['currency_symbol'] = $currencySymbol !== '' ? $currencySymbol : $normalized['currency_display'];

        $currencyUnitAr = trim((string) ($normalized['currency_unit_ar'] ?? 'دينار'));
        $normalized['currency_unit_ar'] = $currencyUnitAr !== '' ? $currencyUnitAr : 'دينار';

        $currencyNameAr = trim((string) ($normalized['currency_name_ar'] ?? 'دينار أردني'));
        $normalized['currency_name_ar'] = $currencyNameAr !== '' ? $currencyNameAr : 'دينار أردني';

        $currencyNameEn = trim((string) ($normalized['currency_name_en'] ?? 'Jordanian Dinar'));
        $normalized['currency_name_en'] = $currencyNameEn !== '' ? $currencyNameEn : 'Jordanian Dinar';

        return $normalized;
    }

    private function currencyContext(array $context): array
    {
        return [
            'code' => (string) ($context['currency_code'] ?? 'JOD'),
            'display' => (string) ($context['currency_display'] ?? ($context['currency_code'] ?? 'JOD')),
            'symbol' => (string) ($context['currency_symbol'] ?? ($context['currency_display'] ?? ($context['currency_code'] ?? 'JOD'))),
            'unit_ar' => (string) ($context['currency_unit_ar'] ?? 'دينار'),
            'name_ar' => (string) ($context['currency_name_ar'] ?? 'دينار أردني'),
            'name_en' => (string) ($context['currency_name_en'] ?? 'Jordanian Dinar'),
        ];
    }
}
