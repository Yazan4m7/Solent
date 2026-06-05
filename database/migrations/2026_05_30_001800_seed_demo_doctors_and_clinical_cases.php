<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SeedDemoDoctorsAndClinicalCases extends Migration
{
    private const CASE_PREFIX = 'SOLENT-DEMO-';

    private array $doctors = [
        [
            'name' => 'Dr. Lina Haddad',
            'phone' => '+962 79 550 1101',
            'clinic_phone' => '+962 6 550 1101',
            'address' => 'Amman - Abdoun',
            'username' => 'demo.lina.haddad',
            'email' => 'lina.haddad.demo@solent.test',
            'opening_balance' => 420.00,
        ],
        [
            'name' => 'Dr. Omar Nasser',
            'phone' => '+962 79 550 1102',
            'clinic_phone' => '+962 6 550 1102',
            'address' => 'Amman - Sweifieh',
            'username' => 'demo.omar.nasser',
            'email' => 'omar.nasser.demo@solent.test',
            'opening_balance' => 180.00,
        ],
        [
            'name' => 'Dr. Sarah Mansour',
            'phone' => '+962 79 550 1103',
            'clinic_phone' => '+962 6 550 1103',
            'address' => 'Irbid - University Street',
            'username' => 'demo.sarah.mansour',
            'email' => 'sarah.mansour.demo@solent.test',
            'opening_balance' => 0.00,
        ],
        [
            'name' => 'Dr. Kareem Alami',
            'phone' => '+962 79 550 1104',
            'clinic_phone' => '+962 6 550 1104',
            'address' => 'Zarqa - New Zarqa',
            'username' => 'demo.kareem.alami',
            'email' => 'kareem.alami.demo@solent.test',
            'opening_balance' => 260.00,
        ],
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

    private array $cases = [
        [
            'case_id' => 'SOLENT-DEMO-2026-001',
            'doctor' => 'demo.lina.haddad',
            'patient_name' => 'Maya A.',
            'created_at' => '2026-01-12 09:15:00',
            'initial_delivery_date' => '2026-01-20 15:00:00',
            'actual_delivery_date' => '2026-01-19 16:25:00',
            'impression' => 'Intraoral Scan',
            'delivered_to_client' => 1,
            'delivered_in_box' => 1,
            'jobs' => [
                ['units' => '11,12', 'type' => 'Crown', 'material' => 'Zirconia Multi Layer', 'color' => 'A2', 'style' => 'Anatomical', 'stage' => -1],
            ],
            'notes' => [
                ['note' => 'Shade verified by photo before sintering.', 'type' => 1, 'created_at' => '2026-01-12 10:00:00'],
                ['note' => 'Delivered one day early; doctor confirmed fit.', 'type' => 2, 'created_at' => '2026-01-19 16:30:00'],
            ],
            'invoice_amount' => 70.00,
            'payment_amount' => 70.00,
        ],
        [
            'case_id' => 'SOLENT-DEMO-2026-002',
            'doctor' => 'demo.omar.nasser',
            'patient_name' => 'Yousef R.',
            'created_at' => '2026-02-03 11:20:00',
            'initial_delivery_date' => '2026-02-17 13:30:00',
            'actual_delivery_date' => '2026-02-18 12:10:00',
            'impression' => 'Silicone Impression',
            'delivered_to_client' => 1,
            'delivered_in_box' => 0,
            'jobs' => [
                ['units' => '24,25,26', 'type' => 'Bridge', 'material' => 'Zirconia Multi Layer', 'color' => 'A3', 'style' => 'Monolithic', 'stage' => -1],
                ['units' => '24,25,26', 'type' => 'Temporary Crown', 'material' => 'PMMA Temporary', 'color' => 'A3', 'style' => 'Temporary', 'stage' => -1],
            ],
            'notes' => [
                ['note' => 'Pontic contact adjusted after doctor feedback.', 'type' => 1, 'created_at' => '2026-02-10 14:10:00'],
            ],
            'invoice_amount' => 159.00,
            'payment_amount' => 100.00,
        ],
        [
            'case_id' => 'SOLENT-DEMO-2026-003',
            'doctor' => 'demo.sarah.mansour',
            'patient_name' => 'Nour K.',
            'created_at' => '2026-03-08 08:45:00',
            'initial_delivery_date' => '2026-03-14 11:00:00',
            'actual_delivery_date' => '2026-03-13 17:40:00',
            'impression' => 'Intraoral Scan',
            'delivered_to_client' => 1,
            'delivered_in_box' => 1,
            'jobs' => [
                ['units' => '31,32,41,42', 'type' => 'Veneer', 'material' => 'E.max Press', 'color' => 'B1', 'style' => 'Layered', 'stage' => -1],
            ],
            'notes' => [
                ['note' => 'High-translucency ingot selected for anterior case.', 'type' => 1, 'created_at' => '2026-03-08 12:00:00'],
                ['note' => 'Final glaze accepted by clinic.', 'type' => 2, 'created_at' => '2026-03-13 17:45:00'],
            ],
            'invoice_amount' => 168.00,
            'payment_amount' => 168.00,
        ],
        [
            'case_id' => 'SOLENT-DEMO-2026-004',
            'doctor' => 'demo.kareem.alami',
            'patient_name' => 'Rami T.',
            'created_at' => '2026-04-02 13:05:00',
            'initial_delivery_date' => '2026-04-16 10:00:00',
            'actual_delivery_date' => '2026-04-15 15:20:00',
            'impression' => 'Model / Cast',
            'delivered_to_client' => 1,
            'delivered_in_box' => 0,
            'jobs' => [
                ['units' => '36', 'type' => 'Implant Crown', 'material' => 'Titanium Abutment', 'color' => 'A2', 'style' => 'Screw retained', 'stage' => -1],
                ['units' => '36', 'type' => 'Implant Crown', 'material' => 'Zirconia Multi Layer', 'color' => 'A2', 'style' => 'Cement retained crown', 'stage' => -1],
            ],
            'notes' => [
                ['note' => 'Custom abutment and zirconia crown delivered together.', 'type' => 1, 'created_at' => '2026-04-15 15:25:00'],
            ],
            'invoice_amount' => 90.00,
            'payment_amount' => 0.00,
        ],
        [
            'case_id' => 'SOLENT-DEMO-2026-005',
            'doctor' => 'demo.lina.haddad',
            'patient_name' => 'Salma H.',
            'created_at' => '2026-05-12 10:40:00',
            'initial_delivery_date' => '2026-06-03 14:00:00',
            'actual_delivery_date' => null,
            'impression' => 'Intraoral Scan',
            'delivered_to_client' => 0,
            'delivered_in_box' => 0,
            'jobs' => [
                ['units' => '14,15', 'type' => 'Crown', 'material' => 'Zirconia Multi Layer', 'color' => 'A1', 'style' => 'Anatomical', 'stage' => 4, 'is_set' => 1],
                ['units' => '14,15', 'type' => 'Temporary Crown', 'material' => 'PMMA Temporary', 'color' => 'A1', 'style' => 'Chairside temporary', 'stage' => -1],
            ],
            'notes' => [
                ['note' => 'Zirconia units are in sintering; temporary already sent.', 'type' => 1, 'created_at' => '2026-05-14 09:30:00'],
            ],
            'invoice_amount' => null,
            'payment_amount' => null,
        ],
        [
            'case_id' => 'SOLENT-DEMO-2026-006',
            'doctor' => 'demo.omar.nasser',
            'patient_name' => 'Adam Q.',
            'created_at' => '2026-05-20 12:30:00',
            'initial_delivery_date' => '2026-06-07 16:30:00',
            'actual_delivery_date' => null,
            'impression' => 'Silicone Impression',
            'delivered_to_client' => 0,
            'delivered_in_box' => 0,
            'jobs' => [
                ['units' => '21', 'type' => 'Crown', 'material' => 'E.max Press', 'color' => 'B2', 'style' => 'Layered', 'stage' => 5, 'assignee' => true, 'is_active' => 1],
            ],
            'notes' => [
                ['note' => 'Press cycle started; verify margin after divesting.', 'type' => 1, 'created_at' => '2026-05-23 10:25:00'],
            ],
            'invoice_amount' => null,
            'payment_amount' => null,
        ],
        [
            'case_id' => 'SOLENT-DEMO-2026-007',
            'doctor' => 'demo.sarah.mansour',
            'patient_name' => 'Layla B.',
            'created_at' => '2026-05-26 09:50:00',
            'initial_delivery_date' => '2026-06-11 12:00:00',
            'actual_delivery_date' => null,
            'impression' => 'Intraoral Scan',
            'delivered_to_client' => 0,
            'delivered_in_box' => 0,
            'jobs' => [
                ['units' => 'lower', 'type' => 'Night Guard', 'material' => 'Surgical Guide Resin', 'color' => 'Clear', 'style' => 'Soft night guard', 'stage' => 3, 'is_set' => 1],
            ],
            'notes' => [
                ['note' => 'Print queued; check occlusal thickness before finishing.', 'type' => 1, 'created_at' => '2026-05-26 11:00:00'],
            ],
            'invoice_amount' => null,
            'payment_amount' => null,
        ],
        [
            'case_id' => 'SOLENT-DEMO-2026-008',
            'doctor' => 'demo.kareem.alami',
            'patient_name' => 'Tala M.',
            'created_at' => '2026-05-29 15:15:00',
            'initial_delivery_date' => '2026-06-13 10:30:00',
            'actual_delivery_date' => null,
            'impression' => 'Intraoral Scan',
            'delivered_to_client' => 0,
            'delivered_in_box' => 0,
            'jobs' => [
                ['units' => 'upper', 'type' => 'Surgical Guide', 'material' => 'Surgical Guide Resin', 'color' => 'Clear', 'style' => 'Pilot guide', 'stage' => 1],
            ],
            'notes' => [
                ['note' => 'Doctor requested implant planning review before design approval.', 'type' => 1, 'created_at' => '2026-05-29 15:30:00'],
            ],
            'invoice_amount' => null,
            'payment_amount' => null,
        ],
    ];

    public function up(): void
    {
        if (! $this->hasRequiredTables()) {
            return;
        }

        DB::transaction(function (): void {
            $now = now();

            $this->seedFoundationData($now);
            $userId = $this->systemUserId($now);
            $doctorIds = $this->seedDoctors($now);
            $materialIds = DB::table('materials')->whereIn('name', array_column($this->materials, 'name'))->pluck('id', 'name');
            $jobTypeIds = DB::table('job_types')->whereIn('name', array_column($this->jobTypes, 'name'))->pluck('id', 'name');

            foreach ($this->cases as $case) {
                if (! isset($doctorIds[$case['doctor']])) {
                    continue;
                }

                DB::table('cases')->updateOrInsert(
                    ['case_id' => $case['case_id']],
                    $this->filterColumns('cases', [
                        'case_id' => $case['case_id'],
                        'patient_name' => $case['patient_name'],
                        'doctor_id' => $doctorIds[$case['doctor']],
                        'impression_type' => $this->impressionTypeId($case['impression'], $now),
                        'initial_delivery_date' => $case['initial_delivery_date'],
                        'actual_delivery_date' => $case['actual_delivery_date'],
                        'delivered_to_client' => $case['delivered_to_client'],
                        'delivered_in_box' => $case['delivered_in_box'],
                        'created_by' => $userId,
                        'created_at' => $case['created_at'],
                        'updated_at' => $now,
                    ])
                );

                $caseId = DB::table('cases')->where('case_id', $case['case_id'])->value('id');

                $this->seedJobs($case, $caseId, $doctorIds[$case['doctor']], $materialIds, $jobTypeIds, $userId, $now);
                $this->seedNotes($case, $caseId, $userId, $now);
                $this->seedDemoCaseLogs($case, $caseId, $userId);
                $this->seedInvoice($case, $caseId, $doctorIds[$case['doctor']], $now);
            }

            $this->seedPayments($doctorIds, $userId, $now);
        });
    }

    public function down(): void
    {
        if (! $this->hasRequiredTables()) {
            return;
        }

        DB::transaction(function (): void {
            $caseIds = DB::table('cases')->where('case_id', 'like', self::CASE_PREFIX . '%')->pluck('id');
            $doctorUsernames = array_column($this->doctors, 'username');
            $doctorIds = DB::table('clients')->whereIn('username', $doctorUsernames)->pluck('id');

            if ($caseIds->isNotEmpty()) {
                DB::table('notes')->whereIn('case_id', $caseIds)->delete();
                DB::table('case_logs')->whereIn('case_id', $caseIds)->delete();
                DB::table('invoices')->whereIn('case_id', $caseIds)->delete();
                DB::table('jobs')->whereIn('case_id', $caseIds)->delete();
                DB::table('cases')->whereIn('id', $caseIds)->delete();
            }

            if ($doctorIds->isNotEmpty()) {
                DB::table('payments')
                    ->whereIn('doctor_id', $doctorIds)
                    ->where('additional_notes', 'like', self::CASE_PREFIX . '%')
                    ->delete();
                if (Schema::hasTable('client_materials')) {
                    DB::table('client_materials')->whereIn('client_id', $doctorIds)->delete();
                }
                DB::table('clients')->whereIn('id', $doctorIds)->delete();
            }

            DB::table('users')->where('username', 'demo.lab.coordinator')->delete();
        });
    }

    private function hasRequiredTables(): bool
    {
        return Schema::hasTable('clients')
            && Schema::hasTable('cases')
            && Schema::hasTable('jobs')
            && Schema::hasTable('notes')
            && Schema::hasTable('case_logs')
            && Schema::hasTable('invoices')
            && Schema::hasTable('payments')
            && Schema::hasTable('users')
            && Schema::hasTable('materials')
            && Schema::hasTable('job_types');
    }

    private function seedFoundationData($now): void
    {
        foreach ($this->jobTypes as $jobType) {
            DB::table('job_types')->updateOrInsert(
                ['name' => $jobType['name']],
                $this->filterColumns('job_types', array_merge($jobType, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]))
            );
        }

        foreach ($this->materials as $material) {
            DB::table('materials')->updateOrInsert(
                ['name' => $material['name']],
                $this->filterColumns('materials', array_merge($material, [
                    'restricted' => 0,
                    'count_as_unit' => 1,
                    'count_in_units_counts_report' => 1,
                    'count_in_job_types_report' => 1,
                    'count_in_qc_report' => 1,
                    'count_in_implants_report' => 1,
                    'is_active' => 1,
                    'default_type_id' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]))
            );
        }
    }

    private function systemUserId($now): int
    {
        $userId = DB::table('users')->where('is_admin', 1)->value('id') ?? DB::table('users')->value('id');

        if ($userId) {
            return (int) $userId;
        }

        DB::table('users')->updateOrInsert(
            ['username' => 'demo.lab.coordinator'],
            $this->filterColumns('users', [
                'first_name' => 'Demo',
                'last_name' => 'Coordinator',
                'name_initials' => 'DC',
                'username' => 'demo.lab.coordinator',
                'email' => 'demo.coordinator@solent.test',
                'phone' => '+962 79 550 1199',
                'password' => Hash::make('demo-clinical-seed'),
                'is_admin' => 1,
                'status' => 1,
                'included_in_reports' => 1,
                'is_developer' => 0,
                'has_photo' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ])
        );

        return (int) DB::table('users')->where('username', 'demo.lab.coordinator')->value('id');
    }

    private function seedDoctors($now)
    {
        foreach ($this->doctors as $doctor) {
            DB::table('clients')->updateOrInsert(
                ['username' => $doctor['username']],
                $this->filterColumns('clients', array_merge($doctor, [
                    'active' => 1,
                    'balance' => 0,
                    'password' => Hash::make('doctor-demo-access'),
                    'opening_balance_date' => '2026-01-01',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]))
            );
        }

        $doctorIds = DB::table('clients')->whereIn('username', array_column($this->doctors, 'username'))->pluck('id', 'username');
        $materialIds = DB::table('materials')->whereIn('name', array_column($this->materials, 'name'))->pluck('id', 'name');

        if (Schema::hasTable('client_materials')) {
            foreach ($doctorIds as $doctorId) {
                foreach ($this->materials as $material) {
                    if (! isset($materialIds[$material['name']])) {
                        continue;
                    }

                    DB::table('client_materials')->updateOrInsert(
                        ['client_id' => $doctorId, 'material_id' => $materialIds[$material['name']]],
                        $this->filterColumns('client_materials', [
                            'client_id' => $doctorId,
                            'material_id' => $materialIds[$material['name']],
                            'price' => $material['price'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])
                    );
                }
            }
        }

        return $doctorIds;
    }

    private function seedJobs(array $case, int $caseId, int $doctorId, $materialIds, $jobTypeIds, int $userId, $now): void
    {
        foreach ($case['jobs'] as $job) {
            if (! isset($materialIds[$job['material']], $jobTypeIds[$job['type']])) {
                continue;
            }

            $unitPrice = round(((float) DB::table('materials')->where('id', $materialIds[$job['material']])->value('price')), 2);

            DB::table('jobs')->updateOrInsert(
                [
                    'case_id' => $caseId,
                    'unit_num' => $job['units'],
                    'type' => $jobTypeIds[$job['type']],
                    'material_id' => $materialIds[$job['material']],
                ],
                $this->filterColumns('jobs', [
                    'unit_num' => $job['units'],
                    'type' => $jobTypeIds[$job['type']],
                    'color' => $job['color'],
                    'style' => $job['style'],
                    'material_id' => $materialIds[$job['material']],
                    'case_id' => $caseId,
                    'doctor_id' => $doctorId,
                    'stage' => $job['stage'],
                    'assignee' => ! empty($job['assignee']) ? $userId : null,
                    'delivery_accepted' => $job['stage'] === 8 ? $userId : null,
                    'unit_price' => $unitPrice,
                    'is_set' => $job['is_set'] ?? ($job['stage'] === -1 ? 1 : null),
                    'is_active' => $job['is_active'] ?? null,
                    'is_rejection' => 0,
                    'has_been_rejected' => 0,
                    'is_repeat' => 0,
                    'is_modification' => 0,
                    'is_redo' => 0,
                    'created_at' => $case['created_at'],
                    'updated_at' => $now,
                ])
            );
        }
    }

    private function seedNotes(array $case, int $caseId, int $userId, $now): void
    {
        foreach ($case['notes'] as $note) {
            DB::table('notes')->updateOrInsert(
                ['case_id' => $caseId, 'note' => $note['note']],
                $this->filterColumns('notes', [
                    'case_id' => $caseId,
                    'note' => $note['note'],
                    'type' => $note['type'],
                    'written_by' => $userId,
                    'created_at' => $note['created_at'],
                    'updated_at' => $now,
                ])
            );
        }
    }

    private function seedDemoCaseLogs(array $case, int $caseId, int $userId): void
    {
        $stages = $case['actual_delivery_date'] === null
            ? $this->activeLogStages($case)
            : [1, 2, 4, 6, 7, 8];

        foreach ($stages as $index => $stage) {
            $createdAt = date('Y-m-d H:i:s', strtotime($case['created_at'] . ' +' . (($index + 1) * 8) . ' hours'));

            DB::table('case_logs')->updateOrInsert(
                ['case_id' => $caseId, 'stage' => $stage, 'is_completion' => 1],
                $this->filterColumns('case_logs', [
                    'user_id' => $userId,
                    'case_id' => $caseId,
                    'stage' => $stage,
                    'is_completion' => 1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ])
            );
        }
    }

    private function activeLogStages(array $case): array
    {
        $highestStage = max(array_column($case['jobs'], 'stage'));

        return array_values(array_filter([1, 2, 3, 4, 5, 6, 7, 8], fn ($stage) => $stage <= $highestStage));
    }

    private function seedInvoice(array $case, int $caseId, int $doctorId, $now): void
    {
        if ($case['invoice_amount'] === null) {
            return;
        }

        DB::table('invoices')->updateOrInsert(
            ['case_id' => $caseId, 'doctor_id' => $doctorId],
            $this->filterColumns('invoices', [
                'status' => 1,
                'amount' => $case['invoice_amount'],
                'amount_before_discount' => $case['invoice_amount'],
                'case_id' => $caseId,
                'doctor_id' => $doctorId,
                'discount_title' => self::CASE_PREFIX . 'clinical-seed',
                'date_applied' => $case['actual_delivery_date'] ?? $now,
                'rejection_invoice' => 0,
                'created_at' => $case['actual_delivery_date'] ?? $case['created_at'],
                'updated_at' => $now,
            ])
        );
    }

    private function seedPayments($doctorIds, int $userId, $now): void
    {
        foreach ($this->cases as $case) {
            if ($case['payment_amount'] === null || $case['payment_amount'] <= 0 || ! isset($doctorIds[$case['doctor']])) {
                continue;
            }

            DB::table('payments')->updateOrInsert(
                ['doctor_id' => $doctorIds[$case['doctor']], 'additional_notes' => self::CASE_PREFIX . $case['case_id']],
                $this->filterColumns('payments', [
                    'amount' => $case['payment_amount'],
                    'notes' => 'Demo payment for ' . $case['patient_name'],
                    'doctor_id' => $doctorIds[$case['doctor']],
                    'collector' => $userId,
                    'received_by' => $userId,
                    'recieved_on' => $case['actual_delivery_date'],
                    'is_credit_note' => 0,
                    'additional_notes' => self::CASE_PREFIX . $case['case_id'],
                    'created_at' => $case['actual_delivery_date'] ?? $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    private function impressionTypeId(string $name, $now): ?int
    {
        if (! Schema::hasTable('impression_types')) {
            return null;
        }

        DB::table('impression_types')->updateOrInsert(
            ['name' => $name],
            $this->filterColumns('impression_types', [
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ])
        );

        return DB::table('impression_types')->where('name', $name)->value('id');
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
