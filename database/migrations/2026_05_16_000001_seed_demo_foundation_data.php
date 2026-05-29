<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedDemoFoundationData extends Migration
{
    private array $impressionTypes = [
        'Intraoral Scan',
        'Silicone Impression',
        'Alginate Impression',
        'Model / Cast',
    ];

    private array $jobTypes = [
        ['name' => 'Crown', 'teeth_or_jaw' => 1, 'a_secondary_item' => 0],
        ['name' => 'Bridge', 'teeth_or_jaw' => 1, 'a_secondary_item' => 0],
        ['name' => 'Veneer', 'teeth_or_jaw' => 1, 'a_secondary_item' => 0],
        ['name' => 'Implant Crown', 'teeth_or_jaw' => 1, 'a_secondary_item' => 0],
        ['name' => 'Surgical Guide', 'teeth_or_jaw' => 2, 'a_secondary_item' => 0],
        ['name' => 'Night Guard', 'teeth_or_jaw' => 2, 'a_secondary_item' => 0],
        ['name' => 'Temporary Crown', 'teeth_or_jaw' => 1, 'a_secondary_item' => 1],
    ];

    private array $materials = [
        ['name' => 'Zirconia Multi Layer', 'price' => 35, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 1, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'E.max Press', 'price' => 42, 'design' => 1, 'mill' => 0, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 1, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'PMMA Temporary', 'price' => 18, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Surgical Guide Resin', 'price' => 24, 'design' => 1, 'mill' => 0, 'print_3d' => 1, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Titanium Abutment', 'price' => 55, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 1, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
    ];

    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('impression_types')) {
            foreach ($this->impressionTypes as $name) {
                DB::table('impression_types')->updateOrInsert(
                    ['name' => $name],
                    ['updated_at' => $now, 'created_at' => $now]
                );
            }
        }

        if (Schema::hasTable('job_types')) {
            foreach ($this->jobTypes as $jobType) {
                DB::table('job_types')->updateOrInsert(
                    ['name' => $jobType['name']],
                    array_merge($jobType, ['updated_at' => $now, 'created_at' => $now])
                );
            }
        }

        if (Schema::hasTable('materials')) {
            foreach ($this->materials as $material) {
                $payload = $this->filterColumns('materials', array_merge($material, [
                    'restricted' => 0,
                    'count_as_unit' => 1,
                    'count_in_units_counts_report' => 1,
                    'count_in_job_types_report' => 1,
                    'count_in_qc_report' => 1,
                    'count_in_implants_report' => 1,
                    'is_active' => 1,
                    'default_type_id' => 0,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]));

                DB::table('materials')->updateOrInsert(['name' => $material['name']], $payload);
            }
        }

        $this->linkMaterialsToJobTypes($now);
    }

    public function down(): void
    {
        if (Schema::hasTable('material_jobtypes')) {
            $materialIds = Schema::hasTable('materials')
                ? DB::table('materials')->whereIn('name', array_column($this->materials, 'name'))->pluck('id')
                : collect();

            $jobTypeIds = Schema::hasTable('job_types')
                ? DB::table('job_types')->whereIn('name', array_column($this->jobTypes, 'name'))->pluck('id')
                : collect();

            if ($materialIds->isNotEmpty() && $jobTypeIds->isNotEmpty()) {
                DB::table('material_jobtypes')
                    ->whereIn('material_id', $materialIds)
                    ->whereIn('jobtype_id', $jobTypeIds)
                    ->delete();
            }
        }

        if (Schema::hasTable('materials')) {
            DB::table('materials')->whereIn('name', array_column($this->materials, 'name'))->delete();
        }

        if (Schema::hasTable('job_types')) {
            DB::table('job_types')->whereIn('name', array_column($this->jobTypes, 'name'))->delete();
        }

        if (Schema::hasTable('impression_types')) {
            DB::table('impression_types')->whereIn('name', $this->impressionTypes)->delete();
        }
    }

    private function linkMaterialsToJobTypes($now): void
    {
        if (! Schema::hasTable('material_jobtypes') || ! Schema::hasTable('materials') || ! Schema::hasTable('job_types')) {
            return;
        }

        $materialIds = DB::table('materials')->whereIn('name', array_column($this->materials, 'name'))->pluck('id', 'name');
        $jobTypeIds = DB::table('job_types')->whereIn('name', array_column($this->jobTypes, 'name'))->pluck('id', 'name');

        $links = [
            'Zirconia Multi Layer' => ['Crown', 'Bridge', 'Implant Crown'],
            'E.max Press' => ['Crown', 'Bridge', 'Veneer'],
            'PMMA Temporary' => ['Temporary Crown', 'Crown', 'Bridge'],
            'Surgical Guide Resin' => ['Surgical Guide'],
            'Titanium Abutment' => ['Implant Crown'],
        ];

        foreach ($links as $materialName => $jobTypeNames) {
            foreach ($jobTypeNames as $jobTypeName) {
                if (! isset($materialIds[$materialName], $jobTypeIds[$jobTypeName])) {
                    continue;
                }

                DB::table('material_jobtypes')->updateOrInsert(
                    [
                        'material_id' => $materialIds[$materialName],
                        'jobtype_id' => $jobTypeIds[$jobTypeName],
                    ],
                    ['updated_at' => $now, 'created_at' => $now]
                );
            }
        }
    }

    private function filterColumns(string $table, array $payload): array
    {
        return array_filter(
            $payload,
            fn ($value, $column) => Schema::hasColumn($table, $column),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
