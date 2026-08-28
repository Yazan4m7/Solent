<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunAuditShortcut extends Command
{
    protected $signature = 'audit
        {target : Use gets to audit safe GET routes}
        {--user= : Username to audit as}
        {--include-unsafe : Also invoke GET routes that can change application data}';

    protected $description = 'Shortcut for application route audits.';

    public function handle(): int
    {
        if (strtolower((string) $this->argument('target')) !== 'gets') {
            $this->error('Use: php artisan audit gets');
            return self::FAILURE;
        }

        return $this->call('routes:audit-get-requests', [
            '--user' => $this->option('user'),
            '--include-unsafe' => (bool) $this->option('include-unsafe'),
        ]);
    }
}
