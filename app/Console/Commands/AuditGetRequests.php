<?php

namespace App\Console\Commands;

use App\JobType;
use App\User;
use App\client;
use App\failureCause;
use App\implant;
use App\material;
use App\sCase;
use App\tag;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditGetRequests extends Command
{
    protected $signature = 'routes:audit-get-requests
        {--user= : Username to audit as; defaults to the first enabled administrator}
        {--json=storage/app/route-load-audit/get-requests-latest.json : Where to save the JSON report}
        {--include-unsafe : Also invoke GET routes that can change application data}';

    protected $description = 'Audit every safe web GET route and report exceptions or server errors.';

    public function handle(): int
    {
        $user = $this->auditUser();

        if (!$user) {
            $this->error('No enabled administrator was found. Pass --user=USERNAME to choose an audit user.');
            return self::FAILURE;
        }

        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $results = [];

        foreach ($this->routesToAudit() as $route) {
            $details = $this->routeDetails($route);

            if (!$this->option('include-unsafe') && $this->isUnsafe($details)) {
                $results[] = $this->result($details, null, null, 'skipped', 'Potentially state-changing GET route.');
                continue;
            }

            $parameters = $this->parametersFor($details);
            if ($parameters === null) {
                $results[] = $this->result($details, null, null, 'skipped', 'No safe record available for route parameter(s).');
                continue;
            }

            $uri = $this->buildUri($details['uri'], $parameters);
            $this->line('Auditing ' . $uri . ' ...');
            $request = Request::create($uri, 'GET');
            $startedAt = microtime(true);

            try {
                Auth::onceUsingId($user->id);
                $response = $kernel->handle($request);
                $kernel->terminate($request, $response);

                $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;
                $state = $status !== null && $status >= 500 ? 'failed' : 'ok';
                $results[] = $this->result($details, $uri, $status, $state, null, $startedAt);
            } catch (Throwable $exception) {
                $results[] = $this->result(
                    $details,
                    $uri,
                    500,
                    'failed',
                    get_class($exception) . ': ' . $exception->getMessage(),
                    $startedAt
                );
            } finally {
                if (Auth::check()) {
                    Auth::logout();
                }

                // Views such as the active-cases list can have a very large rendered body.
                // Drop it before auditing the next route so one page cannot retain memory for
                // the entire audit run.
                unset($response, $request);
                gc_collect_cycles();
                gc_mem_caches();
            }
        }

        $this->writeReport($results, $user);
        $this->printTable($results);

        $failed = collect($results)->where('state', 'failed')->count();
        $skipped = collect($results)->where('state', 'skipped')->count();

        if ($failed > 0) {
            $this->error($failed . ' GET request audit(s) failed.');
            return self::FAILURE;
        }

        $this->info('No GET request audit failures found.' . ($skipped ? ' Skipped: ' . $skipped . '.' : ''));
        return self::SUCCESS;
    }

    private function auditUser(): ?User
    {
        $query = User::query();

        if ($this->option('user')) {
            return $query->where('username', $this->option('user'))->first();
        }

        if (Schema::hasColumn('users', 'is_admin')) {
            $query->where('is_admin', 1);
        }

        if (Schema::hasColumn('users', 'active')) {
            $query->where('active', 1);
        }

        if (Schema::hasColumn('users', 'status')) {
            $query->where('status', 1);
        }

        return $query->orderBy('id')->first();
    }

    private function routesToAudit(): array
    {
        $routes = [];

        foreach (Route::getRoutes()->getRoutes() as $route) {
            if (!in_array('GET', $route->methods(), true) || !$this->isWebRoute($route)) {
                continue;
            }

            $details = $this->routeDetails($route);
            if (str_starts_with($details['uri'], '_debugbar') || str_contains($details['uri'], 'fallbackPlaceholder')) {
                continue;
            }

            $routes[$details['uri'] . '|' . $details['action']] = $route;
        }

        return array_values($routes);
    }

    private function isWebRoute($route): bool
    {
        return in_array('web', $route->gatherMiddleware(), true);
    }

    private function routeDetails($route): array
    {
        return [
            'name' => $route->getName() ?: '(unnamed)',
            'uri' => $route->uri(),
            'action' => (string) $route->getActionName(),
            'parameters' => $this->parameterNames($route->uri()),
        ];
    }

    private function parameterNames(string $uri): array
    {
        preg_match_all('/\{([^}?]+)\??\}/', $uri, $matches);

        return $matches[1] ?? [];
    }

    private function isUnsafe(array $route): bool
    {
        $routeText = strtolower(implode(' ', [$route['name'], $route['uri'], $route['action']]));

        return (bool) preg_match('/assign|delete|destroy|remove|toggle|lock|unlock|restore|finish|complete|reset|send|accept|switch|authenticate|createdummy|createinvoice|testnotification|generatejwt/', $routeText);
    }

    private function parametersFor(array $route): ?array
    {
        if ($route['parameters'] === []) {
            return [];
        }

        $modelClass = $this->modelForRoute($route['uri']);
        $parameters = [];

        foreach ($route['parameters'] as $parameter) {
            if ($parameter === 'stage') {
                $parameters[$parameter] = 1;
                continue;
            }

            if ($parameter === 'amount') {
                $parameters[$parameter] = 1;
                continue;
            }

            if ($parameter === 'x') {
                continue;
            }

            if (!$modelClass) {
                return null;
            }

            $record = $modelClass::query()->orderBy('id')->first();
            if (!$record) {
                return null;
            }

            $parameters[$parameter] = $record->getKey();
        }

        return $parameters;
    }

    private function modelForRoute(string $uri): ?string
    {
        $uri = strtolower($uri);

        return match (true) {
            str_contains($uri, 'job-type') => JobType::class,
            str_contains($uri, 'f-cause') => failureCause::class,
            str_contains($uri, 'implant') => implant::class,
            str_contains($uri, 'material') => material::class,
            str_contains($uri, 'tag') => tag::class,
            str_contains($uri, 'user') => User::class,
            str_contains($uri, 'doctor'), str_contains($uri, 'dentist'), str_contains($uri, 'client') => client::class,
            str_contains($uri, '3d-printing'), str_contains($uri, 'delivery/'), str_contains($uri, 'pressing'),
            str_contains($uri, 'sintering'), str_contains($uri, 'design/'), str_contains($uri, 'milling'),
            str_contains($uri, 'finishing'), str_contains($uri, 'qc/') => User::class,
            str_contains($uri, 'case'), str_contains($uri, 'invoice'), str_contains($uri, 'voucher') => sCase::class,
            default => null,
        };
    }

    private function buildUri(string $uri, array $parameters): string
    {
        $uri = preg_replace_callback('/\{([^}?]+)\?\}/', function (array $match) use ($parameters) {
            return isset($parameters[$match[1]]) ? rawurlencode((string) $parameters[$match[1]]) : '';
        }, $uri);

        foreach ($parameters as $name => $value) {
            $uri = str_replace('{' . $name . '}', rawurlencode((string) $value), $uri);
        }

        return '/' . ltrim(trim($uri, '/'), '/');
    }

    private function result(array $route, ?string $uri, ?int $status, string $state, ?string $error = null, ?float $startedAt = null): array
    {
        return [
            'route' => $route['name'],
            'uri' => $uri,
            'status' => $status,
            'state' => $state,
            'ms' => $startedAt ? (int) round((microtime(true) - $startedAt) * 1000) : null,
            'error' => $error,
        ];
    }

    private function writeReport(array $results, User $user): void
    {
        $path = base_path($this->option('json'));
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode([
            'created_at' => now()->toDateTimeString(),
            'user' => $user->username,
            'results' => $results,
        ], JSON_PRETTY_PRINT));
    }

    private function printTable(array $results): void
    {
        $this->table(
            ['State', 'Status', 'ms', 'Route', 'URI', 'Error'],
            collect($results)->map(fn (array $row) => [
                $row['state'],
                $row['status'],
                $row['ms'],
                $row['route'],
                $row['uri'],
                $row['error'],
            ])->all()
        );
    }
}
