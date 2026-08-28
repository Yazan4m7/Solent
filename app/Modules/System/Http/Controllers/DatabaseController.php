<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index(Request $request)
    {
        $databaseRows = $this->registeredDatabases();
        $metadataError = null;

        try {
            $metadata = $this->databaseMetadata($databaseRows->pluck('database_name'));
        } catch (\Throwable $exception) {
            report($exception);
            $metadata = collect();
            $metadataError = 'Database size and update details are temporarily unavailable.';
        }

        $databaseRows = $databaseRows->map(function (array $database) use ($metadata): array {
            $details = $metadata->get($database['database_name']);
            $lastUpdatedAt = $details && $details->last_updated_at
                ? Carbon::parse($details->last_updated_at)
                : null;

            return array_merge($database, [
                'exists' => $details !== null,
                'size_bytes' => $details ? (int) $details->size_bytes : null,
                'table_count' => $details ? (int) $details->table_count : null,
                'last_updated_at' => $lastUpdatedAt,
            ]);
        });

        $measuredDatabases = $databaseRows->where('exists', true);
        $summary = [
            'database_count' => $databaseRows->count(),
            'measured_count' => $measuredDatabases->count(),
            'total_size_bytes' => $metadataError ? null : $measuredDatabases->sum('size_bytes'),
            'table_count' => $metadataError ? null : $measuredDatabases->sum('table_count'),
        ];

        return view('system.databases.index', [
            'databaseRows' => $databaseRows,
            'summary' => $summary,
            'metadataError' => $metadataError,
            'phpMyAdminUrl' => rtrim($request->getSchemeAndHttpHost(), '/') . '/phpmyadmin',
        ]);
    }

    private function registeredDatabases(): Collection
    {
        $platformDatabase = trim((string) config('tenancy.platform_admin_database'));
        $databases = collect();

        if ($platformDatabase !== '') {
            $databases->push([
                'database_name' => $platformDatabase,
                'owner_name' => 'Platform registry',
                'owner_detail' => 'Tenant configuration and administration',
                'status' => 'system',
                'domain' => config('tenancy.platform_admin_host'),
            ]);
        }

        $tenants = Tenant::query()
            ->with('primaryDomain')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'database_name', 'status']);

        foreach ($tenants as $tenant) {
            $databaseName = trim((string) $tenant->database_name);
            if ($databaseName === '') {
                continue;
            }

            $databases->push([
                'database_name' => $databaseName,
                'owner_name' => $tenant->name,
                'owner_detail' => $tenant->slug,
                'status' => $tenant->status,
                'domain' => optional($tenant->primaryDomain)->host,
            ]);
        }

        return $databases
            ->unique('database_name')
            ->sortBy(fn (array $database) => strtolower($database['database_name']))
            ->values();
    }

    private function databaseMetadata(Collection $databaseNames): Collection
    {
        $databaseNames = $databaseNames
            ->filter(fn ($databaseName) => is_string($databaseName) && $databaseName !== '')
            ->unique()
            ->values();

        if ($databaseNames->isEmpty()) {
            return collect();
        }

        $placeholders = implode(', ', array_fill(0, $databaseNames->count(), '?'));
        $connection = (string) config('tenancy.platform_admin_connection', 'platform_admin');
        $rows = DB::connection($connection)->select(
            "SELECT
                schemata.SCHEMA_NAME AS database_name,
                COUNT(tables.TABLE_NAME) AS table_count,
                COALESCE(SUM(COALESCE(tables.DATA_LENGTH, 0) + COALESCE(tables.INDEX_LENGTH, 0)), 0) AS size_bytes,
                MAX(tables.UPDATE_TIME) AS last_updated_at
            FROM information_schema.SCHEMATA AS schemata
            LEFT JOIN information_schema.TABLES AS tables
                ON tables.TABLE_SCHEMA = schemata.SCHEMA_NAME
            WHERE schemata.SCHEMA_NAME IN ({$placeholders})
            GROUP BY schemata.SCHEMA_NAME",
            $databaseNames->all()
        );

        return collect($rows)->keyBy('database_name');
    }
}
