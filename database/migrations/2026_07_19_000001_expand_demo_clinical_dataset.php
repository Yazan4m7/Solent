<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ExpandDemoClinicalDataset extends Migration
{
    private const CASE_PREFIX = 'SOLENT-DEMO-';

    private array $existingArabicDoctors = [
        'demo.lina.haddad' => 'د. لينا حداد',
        'demo.omar.nasser' => 'د. عمر ناصر',
        'demo.sarah.mansour' => 'د. سارة منصور',
        'demo.kareem.alami' => 'د. كريم العلمي',
    ];

    private array $existingEnglishDoctors = [
        'demo.lina.haddad' => 'Dr. Lina Haddad',
        'demo.omar.nasser' => 'Dr. Omar Nasser',
        'demo.sarah.mansour' => 'Dr. Sarah Mansour',
        'demo.kareem.alami' => 'Dr. Kareem Alami',
    ];

    private array $existingArabicPatients = [
        'SOLENT-DEMO-2026-001' => 'مايا العبدالله',
        'SOLENT-DEMO-2026-002' => 'يوسف الرواشدة',
        'SOLENT-DEMO-2026-003' => 'نور الكيلاني',
        'SOLENT-DEMO-2026-004' => 'رامي الطراونة',
        'SOLENT-DEMO-2026-005' => 'سلمى حجازي',
        'SOLENT-DEMO-2026-006' => 'آدم القضاة',
        'SOLENT-DEMO-2026-007' => 'ليلى بني خالد',
        'SOLENT-DEMO-2026-008' => 'تالا المجالي',
        'SOLENT-DEMO-2026-009' => 'هادي النجار',
        'SOLENT-DEMO-2026-010' => 'دانا الفايز',
        'SOLENT-DEMO-2026-011' => 'زيد المومني',
        'SOLENT-DEMO-2026-012' => 'ريم السالم',
        'SOLENT-DEMO-2026-013' => 'سامر جرادات',
    ];

    private array $existingEnglishPatients = [
        'SOLENT-DEMO-2026-001' => 'Maya A.',
        'SOLENT-DEMO-2026-002' => 'Yousef R.',
        'SOLENT-DEMO-2026-003' => 'Nour K.',
        'SOLENT-DEMO-2026-004' => 'Rami T.',
        'SOLENT-DEMO-2026-005' => 'Salma H.',
        'SOLENT-DEMO-2026-006' => 'Adam Q.',
        'SOLENT-DEMO-2026-007' => 'Layla B.',
        'SOLENT-DEMO-2026-008' => 'Tala M.',
        'SOLENT-DEMO-2026-009' => 'Hadi N.',
        'SOLENT-DEMO-2026-010' => 'Dana F.',
        'SOLENT-DEMO-2026-011' => 'Zaid M.',
        'SOLENT-DEMO-2026-012' => 'Reem S.',
        'SOLENT-DEMO-2026-013' => 'Samer J.',
    ];

    private array $doctors = [
        [
            'name' => 'د. رنا الخطيب',
            'phone' => '+962 79 550 1201',
            'clinic_phone' => '+962 6 550 1201',
            'address' => 'عمّان - دير غبار',
            'username' => 'demo.rana.khatib',
            'email' => 'rana.khatib.demo@solent.test',
            'opening_balance' => 310.00,
        ],
        [
            'name' => 'د. يوسف الشامي',
            'phone' => '+962 79 550 1202',
            'clinic_phone' => '+962 6 550 1202',
            'address' => 'عمّان - خلدا',
            'username' => 'demo.yousef.shami',
            'email' => 'yousef.shami.demo@solent.test',
            'opening_balance' => 145.00,
        ],
        [
            'name' => 'د. نور أبو غزالة',
            'phone' => '+962 79 550 1203',
            'clinic_phone' => '+962 2 550 1203',
            'address' => 'إربد - شارع الحصن',
            'username' => 'demo.nour.abughazaleh',
            'email' => 'nour.abughazaleh.demo@solent.test',
            'opening_balance' => 0.00,
        ],
        [
            'name' => 'د. أحمد العجلوني',
            'phone' => '+962 79 550 1204',
            'clinic_phone' => '+962 5 550 1204',
            'address' => 'السلط - شارع الستين',
            'username' => 'demo.ahmad.ajlouni',
            'email' => 'ahmad.ajlouni.demo@solent.test',
            'opening_balance' => 225.00,
        ],
    ];

    private array $extraJobTypes = [
        ['name' => 'Full Contour Crown', 'teeth_or_jaw' => 0, 'a_secondary_item' => 0],
        ['name' => 'Inlay / Onlay', 'teeth_or_jaw' => 0, 'a_secondary_item' => 0],
        ['name' => 'Maryland Bridge', 'teeth_or_jaw' => 0, 'a_secondary_item' => 0],
        ['name' => 'Custom Abutment', 'teeth_or_jaw' => 0, 'a_secondary_item' => 0],
        ['name' => 'Denture', 'teeth_or_jaw' => 1, 'a_secondary_item' => 0],
        ['name' => 'Orthodontic Retainer', 'teeth_or_jaw' => 1, 'a_secondary_item' => 0],
        ['name' => 'Diagnostic Wax-up', 'teeth_or_jaw' => 1, 'a_secondary_item' => 1],
    ];

    private array $materialDefinitions = [
        ['name' => 'Zirconia Multi Layer', 'price' => 35, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 1, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'E.max Press', 'price' => 42, 'design' => 1, 'mill' => 0, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 1, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'PMMA Temporary', 'price' => 18, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Surgical Guide Resin', 'price' => 24, 'design' => 1, 'mill' => 0, 'print_3d' => 1, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Titanium Abutment', 'price' => 55, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 1, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Monolithic Zirconia', 'price' => 38, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 1, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Lithium Disilicate CAD', 'price' => 45, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'CoCr Alloy', 'price' => 50, 'design' => 1, 'mill' => 1, 'print_3d' => 0, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 1, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Denture Base Resin', 'price' => 30, 'design' => 1, 'mill' => 0, 'print_3d' => 1, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
        ['name' => 'Clear Retainer Resin', 'price' => 22, 'design' => 1, 'mill' => 0, 'print_3d' => 1, 'sinter_furnace' => 0, 'press_furnace' => 0, 'metal_work' => 0, 'finish' => 1, 'qc' => 1, 'delivery' => 1],
    ];

    private array $extraImpressionTypes = [
        'Digital Lab File',
        'PVS Impression',
        'Bite Registration',
        'Printed Model',
    ];

    private array $tags = [
        ['text' => 'عاجل', 'color' => '#ef4444', 'initials' => 'ع', 'icon' => 'fa-solid fa-bolt'],
        ['text' => 'مطابقة لون', 'color' => '#8b5cf6', 'initials' => 'ل', 'icon' => 'fa-solid fa-palette'],
        ['text' => 'تجربة قبل الإنهاء', 'color' => '#0ea5e9', 'initials' => 'ت', 'icon' => 'fa-solid fa-check-double'],
        ['text' => 'زراعة', 'color' => '#14b8a6', 'initials' => 'ز', 'icon' => 'fa-solid fa-tooth'],
        ['text' => 'جسر طويل', 'color' => '#f59e0b', 'initials' => 'ج', 'icon' => 'fa-solid fa-link'],
        ['text' => 'موعد ثابت', 'color' => '#ec4899', 'initials' => 'م', 'icon' => 'fa-regular fa-calendar'],
        ['text' => 'أولوية الطبيب', 'color' => '#6366f1', 'initials' => 'أ', 'icon' => 'fa-solid fa-user-doctor'],
        ['text' => 'يحتاج مراجعة', 'color' => '#64748b', 'initials' => 'ر', 'icon' => 'fa-solid fa-magnifying-glass'],
    ];

    private array $implants = [
        'Straumann BLX',
        'NobelActive',
        'BioHorizons Tapered Pro',
        'Zimmer TSV',
        'Astra Tech EV',
        'Megagen AnyRidge',
        'Dentium SuperLine',
        'Osstem TSIII',
    ];

    private array $failureCauses = [
        'عدم مطابقة اللون',
        'تماس إطباقي مرتفع',
        'هامش غير مطابق',
        'نقطة تماس ضعيفة',
        'كسر أثناء التجربة',
        'تغيير خطة العلاج',
        'بيانات مسح غير مكتملة',
        'تعديل بطلب الطبيب',
    ];

    private array $colors = [
        'A1', 'A2', 'A3', 'A3.5', 'A4', 'B1', 'B2', 'B3',
        'B4', 'C1', 'C2', 'C3', 'C4', 'D2', 'D3', 'D4',
    ];

    public function up(): void
    {
        if (! $this->isDemoDatabase() || ! $this->hasRequiredTables()) {
            return;
        }

        DB::transaction(function (): void {
            $now = now();
            $this->localizeExistingNames($this->existingArabicDoctors, $this->existingArabicPatients);
            $this->seedConfigurationData($now);

            $userId = $this->systemUserId($now);
            $this->seedDoctors($now);
            $doctorIds = DB::table('clients')->whereIn('username', $this->allDoctorUsernames())->pluck('id', 'username');
            $this->linkDoctorsToMaterials($doctorIds, $now);

            $materialIds = DB::table('materials')->whereIn('name', array_column($this->materialDefinitions, 'name'))->pluck('id', 'name');
            $jobTypeIds = DB::table('job_types')->pluck('id', 'name');

            foreach ($this->cases() as $case) {
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

                $caseId = (int) DB::table('cases')->where('case_id', $case['case_id'])->value('id');
                $doctorId = (int) $doctorIds[$case['doctor']];

                $this->seedJobs($case, $caseId, $doctorId, $materialIds, $jobTypeIds, $userId, $now);
                $this->seedNotes($case, $caseId, $userId, $now);
                $this->seedCaseLogs($case, $caseId, $userId);
                $this->seedInvoice($case, $caseId, $doctorId, $now);
                $this->seedPayment($case, $doctorId, $userId, $now);
            }

            $this->linkTagsToDemoCases($userId, $now);
        });
    }

    public function down(): void
    {
        if (! $this->isDemoDatabase() || ! $this->hasRequiredTables()) {
            return;
        }

        DB::transaction(function (): void {
            $newCaseIds = DB::table('cases')
                ->whereBetween('case_id', ['SOLENT-DEMO-2026-014', 'SOLENT-DEMO-2026-026'])
                ->pluck('id');
            $allDemoCaseIds = DB::table('cases')->where('case_id', 'like', self::CASE_PREFIX . '%')->pluck('id');
            $tagIds = Schema::hasTable('tags')
                ? DB::table('tags')->whereIn('text', array_column($this->tags, 'text'))->pluck('id')
                : collect();

            if (Schema::hasTable('case_tags') && $allDemoCaseIds->isNotEmpty() && $tagIds->isNotEmpty()) {
                DB::table('case_tags')->whereIn('case_id', $allDemoCaseIds)->whereIn('tag_id', $tagIds)->delete();
            }

            if ($newCaseIds->isNotEmpty()) {
                DB::table('notes')->whereIn('case_id', $newCaseIds)->delete();
                DB::table('case_logs')->whereIn('case_id', $newCaseIds)->delete();
                DB::table('invoices')->whereIn('case_id', $newCaseIds)->delete();
                DB::table('jobs')->whereIn('case_id', $newCaseIds)->delete();
                DB::table('cases')->whereIn('id', $newCaseIds)->delete();
            }

            DB::table('payments')
                ->where('additional_notes', 'like', self::CASE_PREFIX . 'SOLENT-DEMO-2026-02%')
                ->delete();

            $newDoctorUsernames = array_column($this->doctors, 'username');
            $newDoctorIds = DB::table('clients')->whereIn('username', $newDoctorUsernames)->pluck('id');
            if ($newDoctorIds->isNotEmpty() && Schema::hasTable('client_materials')) {
                DB::table('client_materials')->whereIn('client_id', $newDoctorIds)->delete();
            }
            DB::table('clients')->whereIn('username', $newDoctorUsernames)->delete();

            $extraMaterialNames = array_column($this->extraMaterials(), 'name');
            $extraMaterialIds = DB::table('materials')->whereIn('name', $extraMaterialNames)->pluck('id');
            if ($extraMaterialIds->isNotEmpty()) {
                if (Schema::hasTable('types')) {
                    DB::table('types')->whereIn('material_id', $extraMaterialIds)->delete();
                }
                if (Schema::hasTable('material_jobtypes')) {
                    DB::table('material_jobtypes')->whereIn('material_id', $extraMaterialIds)->delete();
                }
                if (Schema::hasTable('client_materials')) {
                    DB::table('client_materials')->whereIn('material_id', $extraMaterialIds)->delete();
                }
                DB::table('materials')->whereIn('id', $extraMaterialIds)->delete();
            }

            if (Schema::hasTable('material_jobtypes')) {
                $extraJobTypeIds = DB::table('job_types')->whereIn('name', array_column($this->extraJobTypes, 'name'))->pluck('id');
                DB::table('material_jobtypes')->whereIn('jobtype_id', $extraJobTypeIds)->delete();
            }

            DB::table('job_types')->whereIn('name', array_column($this->extraJobTypes, 'name'))->delete();
            DB::table('impression_types')->whereIn('name', $this->extraImpressionTypes)->delete();

            if (Schema::hasTable('tags')) {
                DB::table('tags')->whereIn('text', array_column($this->tags, 'text'))->delete();
            }
            if (Schema::hasTable('implants')) {
                DB::table('implants')->whereIn('name', $this->implants)->delete();
            }
            if (Schema::hasTable('failure_causes')) {
                DB::table('failure_causes')->whereIn('text', $this->failureCauses)->delete();
            }
            if (Schema::hasTable('colors')) {
                DB::table('colors')->whereIn('code', $this->colors)->delete();
            }

            $this->localizeExistingNames($this->existingEnglishDoctors, $this->existingEnglishPatients);
        });
    }

    private function cases(): array
    {
        $definitions = [
            [
                'case_id' => 'SOLENT-DEMO-2026-014', 'doctor' => 'demo.rana.khatib', 'patient_name' => 'جود حداد',
                'created_days_ago' => 150, 'delivery_offset_days' => -137, 'delivery_time' => '14:30', 'actual_offset_days' => -138, 'actual_time' => '16:10',
                'impression' => 'Digital Lab File', 'delivered_to_client' => 1, 'delivered_in_box' => 1,
                'jobs' => [
                    ['units' => '11,12', 'type' => 'Full Contour Crown', 'material' => 'Monolithic Zirconia', 'color' => 'A2', 'style' => 'Monolithic', 'stage' => -1],
                    ['units' => '11,12', 'type' => 'Temporary Crown', 'material' => 'PMMA Temporary', 'color' => 'A2', 'style' => 'Temporary', 'stage' => -1],
                ],
                'notes' => [
                    ['note' => 'تم اعتماد التصميم واللون من الطبيب قبل بدء التصنيع.', 'type' => 1],
                    ['note' => 'سُلّمت الحالة قبل الموعد مع تأكيد جودة الإطباق.', 'type' => 2],
                ],
                'invoice_amount' => 112.00, 'payment_amount' => 112.00,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-015', 'doctor' => 'demo.yousef.shami', 'patient_name' => 'معاذ ناصر',
                'created_days_ago' => 120, 'delivery_offset_days' => -108, 'delivery_time' => '12:00', 'actual_offset_days' => -107, 'actual_time' => '13:20',
                'impression' => 'PVS Impression', 'delivered_to_client' => 1, 'delivered_in_box' => 0,
                'jobs' => [
                    ['units' => '21,22,23', 'type' => 'Veneer', 'material' => 'Lithium Disilicate CAD', 'color' => 'B1', 'style' => 'Layered', 'stage' => -1],
                ],
                'notes' => [
                    ['note' => 'تمت تجربة القشور واعتماد الشكل النهائي من العيادة.', 'type' => 1],
                ],
                'invoice_amount' => 135.00, 'payment_amount' => 100.00,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-016', 'doctor' => 'demo.nour.abughazaleh', 'patient_name' => 'ريتال منصور',
                'created_days_ago' => 90, 'delivery_offset_days' => -78, 'delivery_time' => '11:00', 'actual_offset_days' => -79, 'actual_time' => '15:45',
                'impression' => 'Printed Model', 'delivered_to_client' => 1, 'delivered_in_box' => 1,
                'jobs' => [
                    ['units' => 'upper', 'type' => 'Denture', 'material' => 'Denture Base Resin', 'color' => 'A1', 'style' => 'Full upper denture', 'stage' => -1],
                ],
                'notes' => [
                    ['note' => 'اكتملت تجربة الشمع وتم تثبيت ترتيب الأسنان المعتمد.', 'type' => 1],
                    ['note' => 'تم فحص الحواف واللمعان قبل التسليم النهائي.', 'type' => 2],
                ],
                'invoice_amount' => 30.00, 'payment_amount' => 30.00,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-017', 'doctor' => 'demo.ahmad.ajlouni', 'patient_name' => 'ليث العواملة',
                'created_days_ago' => 60, 'delivery_offset_days' => -49, 'delivery_time' => '10:30', 'actual_offset_days' => -49, 'actual_time' => '12:05',
                'impression' => 'Bite Registration', 'delivered_to_client' => 1, 'delivered_in_box' => 0,
                'jobs' => [
                    ['units' => '36', 'type' => 'Custom Abutment', 'material' => 'CoCr Alloy', 'color' => 'A2', 'style' => 'Screw retained', 'stage' => -1],
                    ['units' => '36', 'type' => 'Implant Crown', 'material' => 'Monolithic Zirconia', 'color' => 'A2', 'style' => 'Cement retained crown', 'stage' => -1],
                ],
                'notes' => [
                    ['note' => 'تم تسليم الدعامة والتاج معاً بعد فحص المطابقة على النموذج.', 'type' => 1],
                ],
                'invoice_amount' => 88.00, 'payment_amount' => 0.00,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-018', 'doctor' => 'demo.rana.khatib', 'patient_name' => 'لمى الخطيب',
                'created_days_ago' => 3, 'delivery_offset_days' => 7, 'delivery_time' => '13:00',
                'impression' => 'Digital Lab File', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [
                    ['units' => 'upper', 'type' => 'Surgical Guide', 'material' => 'Surgical Guide Resin', 'color' => 'Clear', 'style' => 'Pilot guide', 'stage' => 1],
                    ['units' => 'upper', 'type' => 'Diagnostic Wax-up', 'material' => 'PMMA Temporary', 'color' => 'A2', 'style' => 'Diagnostic preview', 'stage' => -1],
                ],
                'notes' => [['note' => 'تم اعتماد التخطيط الأولي ويجري تجهيز دليل الزراعة للطباعة.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-019', 'doctor' => 'demo.yousef.shami', 'patient_name' => 'كرم الشامي',
                'created_days_ago' => 6, 'delivery_offset_days' => 3, 'delivery_time' => '15:30',
                'impression' => 'Intraoral Scan', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => '14,15', 'type' => 'Full Contour Crown', 'material' => 'Monolithic Zirconia', 'color' => 'A3', 'style' => 'Monolithic', 'stage' => 2, 'assignee' => true, 'is_set' => 1]],
                'notes' => [['note' => 'اكتمل التصميم والوحدات بانتظار دورة التفريز.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-020', 'doctor' => 'demo.nour.abughazaleh', 'patient_name' => 'يارا أبو غزالة',
                'created_days_ago' => 2, 'delivery_offset_days' => 6, 'delivery_time' => '11:45',
                'impression' => 'Printed Model', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => 'lower', 'type' => 'Orthodontic Retainer', 'material' => 'Clear Retainer Resin', 'color' => 'Clear', 'style' => 'Clear retainer', 'stage' => 3, 'is_set' => 1]],
                'notes' => [['note' => 'تمت معايرة الطابعة وتنتظر الحالة بدء الطباعة.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-021', 'doctor' => 'demo.ahmad.ajlouni', 'patient_name' => 'سيف العجلوني',
                'created_days_ago' => 9, 'delivery_offset_days' => -1, 'delivery_time' => '14:00',
                'impression' => 'PVS Impression', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => '34,35,36', 'type' => 'Bridge', 'material' => 'Zirconia Multi Layer', 'color' => 'A2', 'style' => 'Anatomical', 'stage' => 4, 'assignee' => true, 'is_set' => 1]],
                'notes' => [['note' => 'الوحدات داخل فرن التلبيد وسيتم فحصها فور اكتمال الدورة.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-022', 'doctor' => 'demo.rana.khatib', 'patient_name' => 'فرح الدباس',
                'created_days_ago' => 5, 'delivery_offset_days' => 1, 'delivery_time' => '16:00',
                'impression' => 'Intraoral Scan', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => '25', 'type' => 'Inlay / Onlay', 'material' => 'E.max Press', 'color' => 'B2', 'style' => 'Pressed ceramic', 'stage' => 5, 'assignee' => true, 'is_active' => 1]],
                'notes' => [['note' => 'بدأت دورة الكبس مع تثبيت رقم السبيكة ودرجة اللون.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-023', 'doctor' => 'demo.yousef.shami', 'patient_name' => 'عمر الزعبي',
                'created_days_ago' => 8, 'delivery_offset_days' => 0, 'delivery_time' => '17:15',
                'impression' => 'Silicone Impression', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => '12,13', 'type' => 'Veneer', 'material' => 'E.max Press', 'color' => 'B1', 'style' => 'Layered', 'stage' => 6, 'assignee' => true, 'is_set' => 1, 'is_active' => 1]],
                'notes' => [['note' => 'تم الكبس وتُستكمل الآن ملامح السطح ونقاط التماس.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-024', 'doctor' => 'demo.nour.abughazaleh', 'patient_name' => 'ميرا الخصاونة',
                'created_days_ago' => 7, 'delivery_offset_days' => 2, 'delivery_time' => '12:30',
                'impression' => 'Model / Cast', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => '22,23,24', 'type' => 'Maryland Bridge', 'material' => 'CoCr Alloy', 'color' => 'A3', 'style' => 'Metal framework', 'stage' => 7]],
                'notes' => [['note' => 'اكتمل التشطيب المعدني والحالة بانتظار فحص الجودة النهائي.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-025', 'doctor' => 'demo.ahmad.ajlouni', 'patient_name' => 'حمزة الشريدة',
                'created_days_ago' => 10, 'delivery_offset_days' => 0, 'delivery_time' => '15:00',
                'impression' => 'Bite Registration', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => 'lower', 'type' => 'Denture', 'material' => 'Denture Base Resin', 'color' => 'A2', 'style' => 'Partial lower denture', 'stage' => 8, 'assignee' => true, 'is_set' => 1, 'is_active' => 1]],
                'notes' => [['note' => 'اجتازت الحالة فحص الجودة وتم قبولها من قسم التسليم.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
            [
                'case_id' => 'SOLENT-DEMO-2026-026', 'doctor' => 'demo.rana.khatib', 'patient_name' => 'رنيم الحوراني',
                'created_days_ago' => 11, 'delivery_offset_days' => 4, 'delivery_time' => '10:45',
                'impression' => 'Digital Lab File', 'delivered_to_client' => 0, 'delivered_in_box' => 0,
                'jobs' => [['units' => '46', 'type' => 'Custom Abutment', 'material' => 'Titanium Abutment', 'color' => 'A3', 'style' => 'Custom abutment', 'stage' => 9, 'assignee' => true, 'is_set' => 1, 'is_active' => 1]],
                'notes' => [['note' => 'تم التفريز وتُجرى مطابقة الدعامة وتشطيب المعدن.', 'type' => 1]],
                'invoice_amount' => null, 'payment_amount' => null,
            ],
        ];

        $today = now()->startOfDay();
        foreach ($definitions as &$case) {
            $createdAt = $today->copy()->subDays($case['created_days_ago'])->setTime(9 + ($case['created_days_ago'] % 6), ($case['created_days_ago'] * 7) % 60);
            [$deliveryHour, $deliveryMinute] = array_map('intval', explode(':', $case['delivery_time']));
            $case['created_at'] = $createdAt->format('Y-m-d H:i:s');
            $case['initial_delivery_date'] = $today->copy()->addDays($case['delivery_offset_days'])->setTime($deliveryHour, $deliveryMinute)->format('Y-m-d H:i:s');
            $case['actual_delivery_date'] = null;

            if (array_key_exists('actual_offset_days', $case)) {
                [$actualHour, $actualMinute] = array_map('intval', explode(':', $case['actual_time']));
                $case['actual_delivery_date'] = $today->copy()->addDays($case['actual_offset_days'])->setTime($actualHour, $actualMinute)->format('Y-m-d H:i:s');
            }

            foreach ($case['notes'] as $index => &$note) {
                $note['created_at'] = $createdAt->copy()->addDay()->addHours($index)->format('Y-m-d H:i:s');
            }
            unset($note);
        }
        unset($case);

        return $definitions;
    }

    private function seedConfigurationData($now): void
    {
        foreach ($this->extraJobTypes as $jobType) {
            DB::table('job_types')->updateOrInsert(
                ['name' => $jobType['name']],
                $this->filterColumns('job_types', array_merge($jobType, ['created_at' => $now, 'updated_at' => $now]))
            );
        }

        foreach ($this->extraMaterials() as $material) {
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

        foreach ($this->extraImpressionTypes as $name) {
            DB::table('impression_types')->updateOrInsert(
                ['name' => $name],
                $this->filterColumns('impression_types', ['name' => $name, 'created_at' => $now, 'updated_at' => $now])
            );
        }

        if (Schema::hasTable('tags')) {
            foreach ($this->tags as $tag) {
                DB::table('tags')->updateOrInsert(
                    ['text' => $tag['text']],
                    $this->filterColumns('tags', array_merge($tag, ['hidden' => 0, 'created_at' => $now, 'updated_at' => $now]))
                );
            }
        }

        foreach ($this->implants as $name) {
            if (Schema::hasTable('implants')) {
                DB::table('implants')->updateOrInsert(
                    ['name' => $name],
                    $this->filterColumns('implants', ['name' => $name, 'created_at' => $now, 'updated_at' => $now])
                );
            }
        }

        foreach ($this->failureCauses as $text) {
            if (Schema::hasTable('failure_causes')) {
                DB::table('failure_causes')->updateOrInsert(
                    ['text' => $text],
                    $this->filterColumns('failure_causes', ['text' => $text, 'created_at' => $now, 'updated_at' => $now])
                );
            }
        }

        foreach ($this->colors as $code) {
            if (Schema::hasTable('colors')) {
                DB::table('colors')->updateOrInsert(
                    ['code' => $code],
                    $this->filterColumns('colors', ['code' => $code, 'created_at' => $now, 'updated_at' => $now])
                );
            }
        }

        $this->linkMaterialsToJobTypes($now);
        $this->seedMaterialTypes($now);
    }

    private function seedDoctors($now): void
    {
        foreach ($this->doctors as $doctor) {
            DB::table('clients')->updateOrInsert(
                ['username' => $doctor['username']],
                $this->filterColumns('clients', array_merge($doctor, [
                    'active' => 1,
                    'balance' => 0,
                    'password' => Hash::make('doctor-demo-access'),
                    'opening_balance_date' => now()->startOfYear()->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]))
            );
        }
    }

    private function linkDoctorsToMaterials($doctorIds, $now): void
    {
        if (! Schema::hasTable('client_materials')) {
            return;
        }

        $materialIds = DB::table('materials')->whereIn('name', array_column($this->materialDefinitions, 'name'))->pluck('id', 'name');
        foreach ($doctorIds as $doctorId) {
            foreach ($this->materialDefinitions as $material) {
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

    private function linkMaterialsToJobTypes($now): void
    {
        if (! Schema::hasTable('material_jobtypes')) {
            return;
        }

        $materialIds = DB::table('materials')->whereIn('name', array_column($this->materialDefinitions, 'name'))->pluck('id', 'name');
        $jobTypeIds = DB::table('job_types')->pluck('id', 'name');
        $links = [
            'Monolithic Zirconia' => ['Crown', 'Bridge', 'Implant Crown', 'Full Contour Crown'],
            'Lithium Disilicate CAD' => ['Crown', 'Veneer', 'Inlay / Onlay'],
            'CoCr Alloy' => ['Bridge', 'Maryland Bridge', 'Custom Abutment'],
            'Denture Base Resin' => ['Denture'],
            'Clear Retainer Resin' => ['Orthodontic Retainer', 'Night Guard'],
            'PMMA Temporary' => ['Diagnostic Wax-up'],
            'Titanium Abutment' => ['Custom Abutment'],
        ];

        foreach ($links as $materialName => $jobTypeNames) {
            foreach ($jobTypeNames as $jobTypeName) {
                if (! isset($materialIds[$materialName], $jobTypeIds[$jobTypeName])) {
                    continue;
                }
                DB::table('material_jobtypes')->updateOrInsert(
                    ['material_id' => $materialIds[$materialName], 'jobtype_id' => $jobTypeIds[$jobTypeName]],
                    $this->filterColumns('material_jobtypes', [
                        'material_id' => $materialIds[$materialName],
                        'jobtype_id' => $jobTypeIds[$jobTypeName],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                );
            }
        }
    }

    private function seedMaterialTypes($now): void
    {
        if (! Schema::hasTable('types')) {
            return;
        }

        $types = [
            'Monolithic Zirconia' => ['High Translucency', 'High Strength'],
            'Lithium Disilicate CAD' => ['Low Translucency', 'Medium Translucency'],
            'CoCr Alloy' => ['Milled Framework', 'Custom Abutment Blank'],
            'Denture Base Resin' => ['Standard Pink', 'Light Pink'],
            'Clear Retainer Resin' => ['0.75 mm', '1.00 mm'],
        ];
        $materialIds = DB::table('materials')->whereIn('name', array_keys($types))->pluck('id', 'name');

        foreach ($types as $materialName => $typeNames) {
            foreach ($typeNames as $typeName) {
                if (! isset($materialIds[$materialName])) {
                    continue;
                }
                DB::table('types')->updateOrInsert(
                    ['name' => $typeName, 'material_id' => $materialIds[$materialName]],
                    $this->filterColumns('types', [
                        'name' => $typeName,
                        'description' => $typeName . ' configuration for ' . $materialName,
                        'material_id' => $materialIds[$materialName],
                        'is_enabled' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                );
            }
        }
    }

    private function seedJobs(array $case, int $caseId, int $doctorId, $materialIds, $jobTypeIds, int $userId, $now): void
    {
        foreach ($case['jobs'] as $job) {
            if (! isset($materialIds[$job['material']], $jobTypeIds[$job['type']])) {
                continue;
            }

            $material = $this->materialByName($job['material']);
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
                    'unit_price' => $material['price'],
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

    private function seedCaseLogs(array $case, int $caseId, int $userId): void
    {
        foreach ($this->caseLogEntries($case) as $index => $entry) {
            $createdAt = date('Y-m-d H:i:s', strtotime($case['created_at'] . ' +' . (($index + 1) * 8) . ' hours'));
            DB::table('case_logs')->updateOrInsert(
                ['case_id' => $caseId, 'stage' => $entry['stage'], 'is_completion' => $entry['is_completion']],
                $this->filterColumns('case_logs', [
                    'user_id' => $userId,
                    'case_id' => $caseId,
                    'stage' => $entry['stage'],
                    'is_completion' => $entry['is_completion'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ])
            );
        }
    }

    private function caseLogEntries(array $case): array
    {
        $entries = [];
        foreach ($case['jobs'] as $job) {
            $path = $this->materialStagePath($job['material']);
            $currentIndex = array_search($job['stage'], $path, true);
            foreach ($path as $index => $stage) {
                if ($job['stage'] !== -1 && ($currentIndex === false || $index > $currentIndex)) {
                    continue;
                }
                $isCompletion = $job['stage'] === -1 || $index < $currentIndex ? 1 : 0;
                $key = $stage . ':' . $isCompletion;
                $entries[$key] = ['stage' => $stage, 'is_completion' => $isCompletion, 'order' => $index];
            }
        }

        usort($entries, function (array $left, array $right): int {
            return [$left['order'], $left['stage'], $left['is_completion']]
                <=> [$right['order'], $right['stage'], $right['is_completion']];
        });

        return array_map(function (array $entry): array {
            unset($entry['order']);
            return $entry;
        }, array_values($entries));
    }

    private function materialStagePath(string $materialName): array
    {
        $material = $this->materialByName($materialName);
        $stageColumns = [
            1 => 'design', 2 => 'mill', 3 => 'print_3d', 4 => 'sinter_furnace',
            5 => 'press_furnace', 9 => 'metal_work', 6 => 'finish', 7 => 'qc', 8 => 'delivery',
        ];

        return array_values(array_filter(
            array_keys($stageColumns),
            fn (int $stage): bool => ! empty($material[$stageColumns[$stage]])
        ));
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
                'discount_title' => self::CASE_PREFIX . 'expanded-clinical-seed',
                'date_applied' => $case['actual_delivery_date'] ?? $now,
                'rejection_invoice' => 0,
                'created_at' => $case['actual_delivery_date'] ?? $case['created_at'],
                'updated_at' => $now,
            ])
        );
    }

    private function seedPayment(array $case, int $doctorId, int $userId, $now): void
    {
        if ($case['payment_amount'] === null || $case['payment_amount'] <= 0) {
            return;
        }

        DB::table('payments')->updateOrInsert(
            ['doctor_id' => $doctorId, 'additional_notes' => self::CASE_PREFIX . $case['case_id']],
            $this->filterColumns('payments', [
                'amount' => $case['payment_amount'],
                'notes' => 'دفعة تجريبية لحالة ' . $case['patient_name'],
                'doctor_id' => $doctorId,
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

    private function linkTagsToDemoCases(int $userId, $now): void
    {
        if (! Schema::hasTable('tags') || ! Schema::hasTable('case_tags')) {
            return;
        }

        $tagIds = DB::table('tags')->whereIn('text', array_column($this->tags, 'text'))->pluck('id', 'text')->values();
        $caseIds = DB::table('cases')->where('case_id', 'like', self::CASE_PREFIX . '%')->orderBy('case_id')->pluck('id');
        if ($tagIds->isEmpty()) {
            return;
        }

        foreach ($caseIds as $index => $caseId) {
            foreach ([$tagIds[$index % $tagIds->count()], $tagIds[($index + 3) % $tagIds->count()]] as $tagId) {
                DB::table('case_tags')->updateOrInsert(
                    ['case_id' => $caseId, 'tag_id' => $tagId],
                    $this->filterColumns('case_tags', [
                        'case_id' => $caseId,
                        'tag_id' => $tagId,
                        'added_by' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                );
            }
        }
    }

    private function localizeExistingNames(array $doctorNames, array $patientNames): void
    {
        foreach ($doctorNames as $username => $name) {
            DB::table('clients')->where('username', $username)->update(['name' => $name]);
        }
        foreach ($patientNames as $caseId => $patientName) {
            DB::table('cases')->where('case_id', $caseId)->update(['patient_name' => $patientName]);
        }
    }

    private function impressionTypeId(string $name, $now): ?int
    {
        DB::table('impression_types')->updateOrInsert(
            ['name' => $name],
            $this->filterColumns('impression_types', ['name' => $name, 'created_at' => $now, 'updated_at' => $now])
        );

        return DB::table('impression_types')->where('name', $name)->value('id');
    }

    private function systemUserId($now): int
    {
        $userId = DB::table('users')->whereNull('deleted_at')->where('is_admin', 1)->value('id')
            ?? DB::table('users')->whereNull('deleted_at')->value('id');
        if ($userId) {
            return (int) $userId;
        }

        DB::table('users')->updateOrInsert(
            ['username' => 'demo.lab.coordinator'],
            $this->filterColumns('users', [
                'first_name' => 'منسق', 'last_name' => 'المختبر', 'name_initials' => 'مخ',
                'username' => 'demo.lab.coordinator', 'email' => 'demo.coordinator@solent.test',
                'phone' => '+962 79 550 1199', 'password' => Hash::make('demo-clinical-seed'),
                'is_admin' => 1, 'status' => 1, 'included_in_reports' => 1,
                'is_developer' => 0, 'has_photo' => 0, 'created_at' => $now, 'updated_at' => $now,
            ])
        );

        return (int) DB::table('users')->where('username', 'demo.lab.coordinator')->value('id');
    }

    private function materialByName(string $name): array
    {
        foreach ($this->materialDefinitions as $material) {
            if ($material['name'] === $name) {
                return $material;
            }
        }

        throw new RuntimeException('Unknown demo material: ' . $name);
    }

    private function extraMaterials(): array
    {
        return array_slice($this->materialDefinitions, 5);
    }

    private function allDoctorUsernames(): array
    {
        return array_merge(array_keys($this->existingArabicDoctors), array_column($this->doctors, 'username'));
    }

    private function hasRequiredTables(): bool
    {
        foreach (['clients', 'cases', 'jobs', 'notes', 'case_logs', 'invoices', 'payments', 'users', 'materials', 'job_types', 'impression_types'] as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function isDemoDatabase(): bool
    {
        $demoDatabase = trim((string) config('domain_context.demo.database', ''));
        return $demoDatabase !== '' && DB::connection()->getDatabaseName() === $demoDatabase;
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
