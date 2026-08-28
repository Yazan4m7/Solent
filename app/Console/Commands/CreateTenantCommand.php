<?php

namespace App\Console\Commands;

use App\Support\Tenancy\TenantProvisioningService;
use Illuminate\Console\Command;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenants:create
        {--slug= : Unique tenant slug}
        {--name= : Tenant display name}
        {--domain= : Primary tenant domain}
        {--database= : Tenant database name}
        {--currency=JOD : Tenant currency code}
        {--admin-name=Tenant Admin : First admin display name}
        {--admin-username= : First admin username}
        {--admin-email= : First admin email}
        {--admin-password= : First admin password}
        {--client-name= : Optional first client display name}
        {--client-username= : Optional first client portal username}
        {--client-email= : Optional first client email}
        {--client-password= : Optional first client portal password}
        {--resume : Resume a failed/provisioning tenant}';

    protected $description = 'Provision a new isolated tenant database and first admin user.';

    public function handle(TenantProvisioningService $provisioning): int
    {
        $payload = [
            'slug' => $this->option('slug') ?: $this->ask('Tenant slug'),
            'name' => $this->option('name') ?: $this->ask('Tenant name'),
            'domain' => $this->option('domain') ?: $this->ask('Primary domain'),
            'database' => $this->option('database'),
            'currency_code' => $this->option('currency') ?: 'JOD',
            'admin_name' => $this->option('admin-name') ?: 'Tenant Admin',
            'admin_username' => $this->option('admin-username'),
            'admin_email' => $this->option('admin-email') ?: $this->ask('Admin email'),
            'admin_password' => $this->option('admin-password') ?: $this->secret('Admin password'),
            'client_name' => $this->option('client-name'),
            'client_username' => $this->option('client-username'),
            'client_email' => $this->option('client-email'),
            'client_password' => $this->option('client-password'),
        ];

        try {
            $tenant = $provisioning->create($payload, (bool) $this->option('resume'));
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Tenant provisioned: ' . $tenant->slug . ' [' . $tenant->status . ']');

        return self::SUCCESS;
    }
}
