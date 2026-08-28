<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class ManageDemoAccounts extends Command
{
    protected $signature = 'demo:accounts
        {count=3 : Number of numbered accounts to create or rotate (1-10)}
        {--prefix=demo : Username prefix, for example demo creates demo1, demo2, ...}
        {--enable=* : Create or rotate only the specified demo usernames}
        {--disable=* : Disable one or more existing demo usernames}';

    protected $description = 'Create, rotate, enable, or disable customer demo logins in the isolated demo database.';

    public function handle(): int
    {
        $connection = 'demo_accounts';

        try {
            $this->configureDemoConnection($connection);

            $disabledUsernames = $this->normalizeManagedUsernames((array) $this->option('disable'));
            $enabledUsernames = $this->normalizeManagedUsernames((array) $this->option('enable'));

            if (count($disabledUsernames) > 0 && count($enabledUsernames) > 0) {
                throw new InvalidArgumentException('Use either --enable or --disable in one command, not both.');
            }

            if (count($disabledUsernames) > 0) {
                return $this->disableAccounts($connection, $disabledUsernames);
            }

            if (count($enabledUsernames) > 0) {
                return $this->provisionAccounts($connection, $enabledUsernames);
            }

            return $this->provisionAccounts($connection, $this->numberedUsernames());
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            if (is_array(config('database.connections.' . $connection))) {
                DB::disconnect($connection);
            }
        }
    }

    public function configureDemoConnection(string $connection): void
    {
        if (! (bool) config('domain_context.demo.enabled', false)) {
            throw new InvalidArgumentException('Demo mode must be enabled before managing demo accounts.');
        }

        $database = trim((string) config('domain_context.demo.database', ''));
        if ($database === '') {
            throw new InvalidArgumentException('DEMO_DB_DATABASE must be configured before managing demo accounts.');
        }

        $templateName = (string) config('tenancy.tenant_connection_template', 'mysql');
        $template = (array) config('database.connections.' . $templateName, config('database.connections.mysql', []));
        if (count($template) === 0) {
            throw new InvalidArgumentException('The tenant database connection template is not configured.');
        }

        $driver = (string) ($template['driver'] ?? 'mysql');
        if ($driver === 'mysql') {
            if (! preg_match('/^[A-Za-z0-9_]+$/', $database)) {
                throw new InvalidArgumentException('DEMO_DB_DATABASE contains unsupported characters.');
            }

            foreach ($this->protectedDatabaseNames($templateName) as $protectedDatabase) {
                if (strcasecmp($database, $protectedDatabase) === 0) {
                    throw new InvalidArgumentException('DEMO_DB_DATABASE must be isolated from every production or landlord database.');
                }
            }

            $this->assertRegisteredTenantIsDemo($database);
        }

        unset($template['url']);
        $template['database'] = $database;
        config()->set('database.connections.' . $connection, $template);
        DB::purge($connection);
        DB::reconnect($connection);

        $connectedDatabase = (string) DB::connection($connection)->getDatabaseName();
        if ($driver === 'mysql' && strcasecmp($connectedDatabase, $database) !== 0) {
            throw new InvalidArgumentException('The demo connection resolved to a different database than DEMO_DB_DATABASE.');
        }

        if (! Schema::connection($connection)->hasTable('users')) {
            throw new InvalidArgumentException('The configured demo database does not contain a users table.');
        }

        $columns = array_flip(Schema::connection($connection)->getColumnListing('users'));
        foreach (['id', 'username', 'password', 'is_admin'] as $requiredColumn) {
            if (! isset($columns[$requiredColumn])) {
                throw new InvalidArgumentException("The demo users table must contain a {$requiredColumn} column.");
            }
        }

        if (! isset($columns['status']) && ! isset($columns['active'])) {
            throw new InvalidArgumentException('The demo users table must contain a status or active column.');
        }
    }

    private function protectedDatabaseNames(string $templateName): array
    {
        $databases = [
            config('domain_context.default.database'),
        ];

        foreach ((array) config('domain_context.hosts', []) as $context) {
            if (! is_array($context) || strtoupper((string) ($context['country_code'] ?? '')) === 'DEMO') {
                continue;
            }

            $databases[] = $context['database'] ?? null;
        }

        $connectionNames = array_unique(array_filter([
            config('database.default'),
            config('tenancy.landlord_connection', 'landlord'),
            $templateName,
        ]));

        foreach ($connectionNames as $connectionName) {
            $databases[] = config('database.connections.' . $connectionName . '.database');
        }

        return array_values(array_unique(array_filter(array_map(function ($database): string {
            return trim((string) $database);
        }, $databases))));
    }

    private function assertRegisteredTenantIsDemo(string $database): void
    {
        $landlordConnection = trim((string) config('tenancy.landlord_connection', 'landlord'));
        if ($landlordConnection === '' || ! is_array(config('database.connections.' . $landlordConnection))) {
            throw new InvalidArgumentException('The landlord connection must be configured before managing demo accounts.');
        }

        try {
            if (! Schema::connection($landlordConnection)->hasTable('tenants')) {
                return;
            }

            $tenant = DB::connection($landlordConnection)
                ->table('tenants')
                ->select('id')
                ->whereRaw('LOWER(database_name) = ?', [strtolower($database)])
                ->first();

            if (! $tenant) {
                return;
            }

            if (! Schema::connection($landlordConnection)->hasTable('tenant_domains')) {
                throw new InvalidArgumentException('The registered tenant database cannot be verified because tenant_domains is missing.');
            }

            $registeredHosts = DB::connection($landlordConnection)
                ->table('tenant_domains')
                ->where('tenant_id', $tenant->id)
                ->pluck('host')
                ->map(function ($host): string {
                    return $this->normalizeHost($host);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new InvalidArgumentException(
                'Unable to verify DEMO_DB_DATABASE against the landlord tenant registry.',
                0,
                $exception
            );
        }

        $this->assertRegisteredHostsAreDemo($registeredHosts);
    }

    private function assertRegisteredHostsAreDemo(array $registeredHosts): void
    {
        $normalizedHosts = array_values(array_unique(array_filter(array_map(function ($host): string {
            return $this->normalizeHost($host);
        }, $registeredHosts))));
        $demoHosts = $this->configuredDemoHosts();

        if (count($normalizedHosts) === 0 || count(array_diff($normalizedHosts, $demoHosts)) > 0) {
            throw new InvalidArgumentException(
                'DEMO_DB_DATABASE is registered to a non-demo or unverified tenant domain.'
            );
        }
    }

    private function configuredDemoHosts(): array
    {
        $hosts = (array) config('domain_context.demo.hosts', []);

        foreach ((array) config('domain_context.hosts', []) as $host => $context) {
            if (is_array($context) && strtoupper((string) ($context['country_code'] ?? '')) === 'DEMO') {
                $hosts[] = $host;
            }
        }

        return array_values(array_unique(array_filter(array_map(function ($host): string {
            return $this->normalizeHost($host);
        }, $hosts))));
    }

    private function normalizeHost($host): string
    {
        $normalized = strtolower(trim((string) $host));

        return strpos($normalized, 'www.') === 0 ? substr($normalized, 4) : $normalized;
    }

    private function numberedUsernames(): array
    {
        $count = filter_var($this->argument('count'), FILTER_VALIDATE_INT);
        if ($count === false || $count < 1 || $count > 10) {
            throw new InvalidArgumentException('Account count must be a whole number between 1 and 10.');
        }

        $prefix = strtolower(trim((string) $this->option('prefix')));
        if (! preg_match('/^[a-z][a-z0-9._-]{1,30}$/', $prefix)) {
            throw new InvalidArgumentException('The prefix must start with a letter and use only letters, numbers, dots, underscores, or hyphens.');
        }

        $usernames = [];
        for ($number = 1; $number <= $count; $number++) {
            $usernames[] = $prefix . $number;
        }

        return $this->normalizeManagedUsernames($usernames);
    }

    private function normalizeManagedUsernames(array $usernames): array
    {
        $normalized = array_values(array_unique(array_filter(array_map(function ($username): string {
            return strtolower(trim((string) $username));
        }, $usernames))));

        if (count($normalized) > 10) {
            throw new InvalidArgumentException('No more than 10 demo accounts may be managed at once.');
        }

        $primaryDemoUsername = strtolower(trim((string) config('domain_context.demo.user.username', 'demo')));
        foreach ($normalized as $username) {
            if (! preg_match('/^[a-z][a-z0-9._-]{1,32}$/', $username)) {
                throw new InvalidArgumentException("Invalid demo username: {$username}");
            }

            if (strcasecmp($username, $primaryDemoUsername) === 0) {
                throw new InvalidArgumentException('The primary demo owner account cannot be managed by this command.');
            }
        }

        return $normalized;
    }

    private function provisionAccounts(string $connection, array $usernames): int
    {
        $columns = array_flip(Schema::connection($connection)->getColumnListing('users'));
        $rows = [];

        DB::connection($connection)->transaction(function () use ($connection, $columns, $usernames, &$rows): void {
            foreach ($usernames as $index => $username) {
                $password = $this->generatePassword();
                $existing = DB::connection($connection)->table('users')->where('username', $username)->first();
                $displayNumber = preg_match('/(\d+)$/', $username, $matches) ? (int) $matches[1] : $index + 1;

                $candidate = [
                    'first_name' => 'Demo',
                    'last_name' => sprintf('%02d', $displayNumber),
                    'name' => 'Demo Customer ' . $displayNumber,
                    'name_initials' => 'D' . $displayNumber,
                    'username' => $username,
                    'email' => $username . '@demo.solent.test',
                    'phone' => null,
                    'password' => Hash::make($password),
                    'is_admin' => 1,
                    'status' => 1,
                    'active' => 1,
                    'included_in_reports' => 0,
                    'is_developer' => 0,
                    'has_photo' => 0,
                    'email_verified_at' => now(),
                    'remember_token' => null,
                    'deleted_at' => null,
                    'updated_at' => now(),
                ];

                $payload = array_intersect_key($candidate, $columns);

                if ($existing) {
                    DB::connection($connection)->table('users')->where('id', $existing->id)->update($payload);
                    $userId = (int) $existing->id;
                    $state = 'rotated and enabled';
                } else {
                    if (isset($columns['created_at'])) {
                        $payload['created_at'] = now();
                    }
                    $userId = (int) DB::connection($connection)->table('users')->insertGetId($payload);
                    $state = 'created';
                }

                Cache::forget('user' . $userId);
                $rows[] = [$username, $password, $state];
            }
        });

        $this->table(['Username', 'Password', 'Result'], $rows);
        $this->warn('Passwords are shown once. Store and share them securely; running the command again rotates them.');

        return self::SUCCESS;
    }

    private function disableAccounts(string $connection, array $usernames): int
    {
        $columns = array_flip(Schema::connection($connection)->getColumnListing('users'));
        $rows = [];

        DB::connection($connection)->transaction(function () use ($connection, $columns, $usernames, &$rows): void {
            foreach ($usernames as $username) {
                $user = DB::connection($connection)->table('users')->where('username', $username)->first();
                if (! $user) {
                    $rows[] = [$username, 'not found'];
                    continue;
                }

                $payload = [
                    'password' => Hash::make($this->generatePassword()),
                ];
                if (isset($columns['status'])) {
                    $payload['status'] = 0;
                }
                if (isset($columns['active'])) {
                    $payload['active'] = 0;
                }
                if (isset($columns['remember_token'])) {
                    $payload['remember_token'] = bin2hex(random_bytes(30));
                }
                if (isset($columns['updated_at'])) {
                    $payload['updated_at'] = now();
                }

                DB::connection($connection)->table('users')->where('id', $user->id)->update($payload);
                Cache::forget('user' . $user->id);
                $rows[] = [$username, 'disabled and credentials revoked'];
            }
        });

        $this->table(['Username', 'Result'], $rows);

        return self::SUCCESS;
    }

    private function generatePassword(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $password = [
            'ABCDEFGHJKLMNPQRSTUVWXYZ'[random_int(0, 23)],
            'abcdefghijkmnopqrstuvwxyz'[random_int(0, 24)],
            '23456789'[random_int(0, 7)],
            '!@#$%'[random_int(0, 4)],
        ];

        while (count($password) < 14) {
            $password[] = $characters[random_int(0, strlen($characters) - 1)];
        }

        for ($index = count($password) - 1; $index > 0; $index--) {
            $swapIndex = random_int(0, $index);
            [$password[$index], $password[$swapIndex]] = [$password[$swapIndex], $password[$index]];
        }

        return implode('', $password);
    }
}
