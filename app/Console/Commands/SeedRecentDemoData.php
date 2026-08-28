<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class SeedRecentDemoData extends Command
{
    private const CASE_PREFIX = 'DEMO-RECENT-';
    private const PAYMENT_PREFIX = 'DEMO-RECENT-PAY-';
    private const CASES_PER_CLIENT = 10;

    protected $signature = 'demo:data
        {--apply : Replace the previously generated recent batch and write a fresh one}';

    protected $description = 'Preview or seed recent synthetic clinical and payment activity in the isolated demo database.';

    private array $columnCache = [];

    private array $patientFirstNames = [
        'ليان', 'آدم', 'تالا', 'يزن', 'جود', 'سلمى', 'عمر', 'نور', 'كريم', 'لانا',
        'سيف', 'رنا', 'ليث', 'دانا', 'جنى', 'حمزة', 'ريم', 'هادي', 'فرح', 'رامي',
    ];

    private array $patientLastNames = [
        'الحداد', 'النجار', 'المجالي', 'الخطيب', 'الرواشدة', 'العجلوني', 'القضاة', 'الطراونة',
        'الزعبي', 'العمري', 'المومني', 'الشامي', 'حجازي', 'الخصاونة', 'السالم', 'الحوراني',
        'العبادي', 'بني خالد', 'الرفاعي', 'منصور',
    ];

    public function handle(): int
    {
        $connection = 'demo_recent_data';

        try {
            app(ManageDemoAccounts::class)->configureDemoConnection($connection);
            $this->assertRequiredSchema($connection);

            $clients = $this->activeClients($connection);
            if ($clients->isEmpty()) {
                throw new InvalidArgumentException('The demo database does not contain any active clients to seed.');
            }

            $pairs = $this->compatiblePairs($connection);
            if (count($pairs) < 3) {
                throw new InvalidArgumentException('The demo database needs at least three compatible material and job-type pairs.');
            }

            $userId = $this->systemUserId($connection);
            $failureCauseId = $this->failureCauseId($connection);
            $today = now()->startOfDay();
            $paymentCount = 0;
            $materialCounts = [];
            $jobTypeCounts = [];

            foreach ($clients as $clientIndex => $client) {
                $blueprints = $this->buildClientCaseBlueprints(
                    (int) $client->id,
                    (int) $clientIndex,
                    $pairs,
                    $today
                );
                $materialCounts[] = count(array_unique(array_map(
                    fn (array $case): int => (int) $case['pair']['material_id'],
                    $blueprints
                )));
                $jobTypeCounts[] = count(array_unique(array_map(
                    fn (array $case): int => (int) $case['pair']['job_type_id'],
                    $blueprints
                )));
                $paymentCount += $this->paymentCountForClient((int) $client->id);
            }

            $this->table(['Planned item', 'Count'], [
                ['Existing clients kept', $clients->count()],
                ['New recent cases', $clients->count() * self::CASES_PER_CLIENT],
                ['New payments', $paymentCount],
                ['Distinct materials per client', min($materialCounts) . '-' . max($materialCounts)],
                ['Distinct job types per client', min($jobTypeCounts) . '-' . max($jobTypeCounts)],
                ['New tags', 0],
            ]);

            if (! (bool) $this->option('apply')) {
                $this->warn('Dry run only. Re-run with --apply after backing up the isolated demo database.');

                return self::SUCCESS;
            }

            $summary = DB::connection($connection)->transaction(function () use (
                $connection,
                $clients,
                $pairs,
                $userId,
                $failureCauseId,
                $today
            ): array {
                $this->clearPreviousBatch($connection);

                $summary = [
                    'cases' => 0,
                    'jobs' => 0,
                    'discounts' => 0,
                    'payments' => 0,
                    'repeats' => 0,
                    'modifications' => 0,
                ];

                foreach ($clients as $clientIndex => $client) {
                    $clientSummary = $this->seedClient(
                        $connection,
                        $client,
                        (int) $clientIndex,
                        $pairs,
                        $userId,
                        $failureCauseId,
                        $today
                    );

                    foreach ($summary as $key => $value) {
                        $summary[$key] += $clientSummary[$key];
                    }
                }

                $this->recalculateClientBalances($connection, $clients->pluck('id')->map(fn ($id): int => (int) $id)->all());

                return $summary;
            });

            $this->table(['Created item', 'Count'], [
                ['Cases', $summary['cases']],
                ['Jobs', $summary['jobs']],
                ['Discounted cases', $summary['discounts']],
                ['Payments', $summary['payments']],
                ['Repeat cases', $summary['repeats']],
                ['Modification cases', $summary['modifications']],
                ['Tags', 0],
            ]);
            $this->info('Recent demo activity was seeded successfully.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            if (is_array(config('database.connections.' . $connection))) {
                DB::disconnect($connection);
            }
        }
    }

    private function seedClient(
        string $connection,
        object $client,
        int $clientIndex,
        array $pairs,
        int $userId,
        int $failureCauseId,
        Carbon $today
    ): array {
        $clientId = (int) $client->id;
        $blueprints = $this->buildClientCaseBlueprints($clientId, $clientIndex, $pairs, $today);
        $impressionTypeIds = $this->impressionTypeIds($connection);
        $clientPrices = $this->clientMaterialPrices($connection, $clientId);
        $caseIds = [];
        $jobIds = [];
        $jobDefinitions = [];
        $invoiceTotal = 0.0;
        $summary = [
            'cases' => 0,
            'jobs' => 0,
            'discounts' => 0,
            'payments' => 0,
            'repeats' => 0,
            'modifications' => 0,
        ];

        foreach ($blueprints as $caseIndex => $blueprint) {
            $repeatOf = $blueprint['repeat_of'];
            $casePayload = $this->filterColumns($connection, 'cases', [
                'case_id' => $blueprint['case_id'],
                'patient_name' => $blueprint['patient_name'],
                'initial_delivery_date' => $blueprint['initial_delivery_date'],
                'actual_delivery_date' => $blueprint['actual_delivery_date'],
                'delivered_to_client' => $blueprint['delivered_to_client'],
                'delivered_in_box' => $blueprint['delivered_to_client'],
                'doctor_id' => $clientId,
                'impression_type' => count($impressionTypeIds) > 0
                    ? $impressionTypeIds[($clientIndex + $caseIndex) % count($impressionTypeIds)]
                    : null,
                'locked' => $blueprint['is_repeat'] || $blueprint['is_modification'] ? 1 : 0,
                'created_by' => $userId,
                'contains_modification' => $blueprint['is_modification'] ? 1 : 0,
                'first_case_if_repeated' => $repeatOf !== null ? ($caseIds[$repeatOf] ?? null) : null,
                'created_at' => $blueprint['created_at'],
                'updated_at' => $today,
            ]);

            $caseIds[$caseIndex] = (int) DB::connection($connection)->table('cases')->insertGetId($casePayload);
            $summary['cases']++;

            $pair = $blueprint['pair'];
            $unitPrice = (float) ($clientPrices[(int) $pair['material_id']] ?? $pair['material_price']);
            $baseJob = [
                'unit_num' => $blueprint['units'],
                'type' => (int) $pair['job_type_id'],
                'color' => $blueprint['color'],
                'style' => $blueprint['style'],
                'material_id' => (int) $pair['material_id'],
                'case_id' => $caseIds[$caseIndex],
                'doctor_id' => $clientId,
                'stage' => $blueprint['stage'],
                'assignee' => $blueprint['stage'] > 1 ? $userId : null,
                'delivery_accepted' => $blueprint['stage'] === 8 ? $userId : null,
                'unit_price' => $unitPrice,
                'is_set' => 1,
                'is_active' => $blueprint['stage'] === -1 ? null : 1,
                'is_rejection' => 0,
                'has_been_rejected' => 0,
                'is_repeat' => $blueprint['is_repeat'] ? 1 : 0,
                'is_modification' => 0,
                'is_redo' => 0,
                'original_job_id' => $repeatOf !== null ? ($jobIds[$repeatOf] ?? null) : null,
                'created_at' => $blueprint['created_at'],
                'updated_at' => $today,
            ];
            $jobIds[$caseIndex] = (int) DB::connection($connection)->table('jobs')->insertGetId(
                $this->filterColumns($connection, 'jobs', $baseJob)
            );
            $summary['jobs']++;
            $jobDefinitions[$caseIndex] = [array_merge($baseJob, ['pair' => $pair])];

            if ($repeatOf !== null) {
                DB::connection($connection)->table('jobs')->where('id', $jobIds[$repeatOf])->update(
                    $this->filterColumns($connection, 'jobs', [
                        'repeated_job_id' => $jobIds[$caseIndex],
                        'updated_at' => $today,
                    ])
                );
                DB::connection($connection)->table('cases')->where('id', $caseIds[$repeatOf])->update(
                    $this->filterColumns($connection, 'cases', ['locked' => 1, 'updated_at' => $today])
                );
                $this->insertFailureLog(
                    $connection,
                    $caseIds[$caseIndex],
                    $caseIds[$repeatOf],
                    1,
                    $failureCauseId,
                    $userId,
                    'إعادة تجريبية لعرض مسار الجودة.',
                    null,
                    $blueprint['created_at']
                );
                $summary['repeats']++;
            }

            if ($blueprint['is_modification']) {
                $modifiedJob = array_merge($baseJob, [
                    'stage' => 6,
                    'assignee' => $userId,
                    'delivery_accepted' => null,
                    'is_active' => 1,
                    'is_repeat' => 0,
                    'is_modification' => 1,
                    'original_job_id' => $jobIds[$caseIndex],
                ]);
                $modifiedJobId = (int) DB::connection($connection)->table('jobs')->insertGetId(
                    $this->filterColumns($connection, 'jobs', $modifiedJob)
                );
                DB::connection($connection)->table('jobs')->where('id', $jobIds[$caseIndex])->update(
                    $this->filterColumns($connection, 'jobs', [
                        'modified_job_id' => $modifiedJobId,
                        'updated_at' => $today,
                    ])
                );
                $jobDefinitions[$caseIndex][] = array_merge($modifiedJob, ['pair' => $pair]);
                $this->insertFailureLog(
                    $connection,
                    $caseIds[$caseIndex],
                    null,
                    2,
                    $failureCauseId,
                    $userId,
                    'تعديل تجريبي بطلب العيادة.',
                    $blueprint['prior_delivery_date'],
                    $blueprint['created_at']
                );
                $summary['jobs']++;
                $summary['modifications']++;
            }

            $gross = $blueprint['is_repeat']
                ? 0.0
                : round($this->unitCount($blueprint['units']) * $unitPrice, 2);
            $discount = $blueprint['discount_rate'] > 0 && $gross > 0
                ? round(min(25, max(5, $gross * $blueprint['discount_rate'])), 2)
                : 0.0;
            $net = round(max(0, $gross - $discount), 2);

            if ($discount > 0) {
                DB::connection($connection)->table('discounts')->insert(
                    $this->filterColumns($connection, 'discounts', [
                        'case_id' => $caseIds[$caseIndex],
                        'reason' => 'خصم تجريبي للحالة',
                        'discount' => $discount,
                        'created_at' => $blueprint['created_at'],
                        'updated_at' => $today,
                    ])
                );
                $summary['discounts']++;
            }

            DB::connection($connection)->table('invoices')->insert(
                $this->filterColumns($connection, 'invoices', [
                    'status' => $blueprint['invoice_status'],
                    'amount' => $net,
                    'amount_before_discount' => $gross,
                    'case_id' => $caseIds[$caseIndex],
                    'doctor_id' => $clientId,
                    'discount_title' => null,
                    'date_applied' => $blueprint['invoice_status'] === 1
                        ? $blueprint['invoice_date']
                        : null,
                    'rejection_invoice' => 0,
                    'created_at' => $blueprint['created_at'],
                    'updated_at' => $today,
                ])
            );

            if ($blueprint['invoice_status'] === 1) {
                $invoiceTotal += $net;
            }

            DB::connection($connection)->table('notes')->insert(
                $this->filterColumns($connection, 'notes', [
                    'case_id' => $caseIds[$caseIndex],
                    'note' => $blueprint['note'],
                    'type' => 1,
                    'written_by' => $userId,
                    'created_at' => Carbon::parse($blueprint['created_at'])->addHours(4),
                    'updated_at' => $today,
                ])
            );

            $this->insertCaseLogs(
                $connection,
                $caseIds[$caseIndex],
                $userId,
                $jobDefinitions[$caseIndex],
                Carbon::parse($blueprint['created_at'])
            );
        }

        $summary['payments'] = $this->seedPayments(
            $connection,
            $clientId,
            $clientIndex,
            $userId,
            $invoiceTotal,
            $today
        );

        return $summary;
    }

    private function buildClientCaseBlueprints(
        int $clientId,
        int $clientIndex,
        array $pairs,
        Carbon $today
    ): array {
        $selectedPairs = $this->selectPairsForClient($clientId, $clientIndex, $pairs);
        $blueprints = [];

        for ($caseIndex = 0; $caseIndex < self::CASES_PER_CLIENT; $caseIndex++) {
            $pair = $selectedPairs[$caseIndex % count($selectedPairs)];
            $isRepeat = $caseIndex === 8;
            $isModification = $caseIndex === 7;
            $isCompleted = $caseIndex < 6;
            $createdDaysAgo = $isCompleted || $isModification
                ? 10 + $this->seededInt("created-complete-{$clientId}-{$caseIndex}", 0, 34)
                : 1 + $this->seededInt("created-active-{$clientId}-{$caseIndex}", 0, 10);
            $createdAt = $today->copy()
                ->subDays($createdDaysAgo)
                ->setTime(
                    8 + $this->seededInt("hour-{$clientId}-{$caseIndex}", 0, 7),
                    $this->seededInt("minute-{$clientId}-{$caseIndex}", 0, 11) * 5
                );
            $priorDeliveryDate = $createdAt->copy()->addDays(5 + $this->seededInt("actual-{$clientId}-{$caseIndex}", 0, 3));
            $initialDeliveryDate = $isCompleted
                ? $createdAt->copy()->addDays(7 + $this->seededInt("due-{$clientId}-{$caseIndex}", 0, 4))
                : $today->copy()->addDays(1 + $this->seededInt("future-due-{$clientId}-{$caseIndex}", 0, 8))->setTime(14, 0);
            $actualDeliveryDate = $isCompleted ? $priorDeliveryDate : null;
            $invoiceStatus = $isCompleted ? 1 : 0;

            if ($isModification) {
                $actualDeliveryDate = null;
                $invoiceStatus = 1;
                $initialDeliveryDate = $today->copy()->addDays(5)->setTime(15, 0);
            }

            $stagePath = $pair['stage_path'] ?? $this->stagePath($pair);
            $stage = $isCompleted || $isModification
                ? -1
                : $stagePath[$this->seededInt("stage-{$clientId}-{$caseIndex}", 0, count($stagePath) - 1)];
            $caseNumber = str_pad((string) ($caseIndex + 1), 2, '0', STR_PAD_LEFT);
            $patientName = $this->patientName($clientIndex, $caseIndex);

            $blueprints[] = [
                'case_id' => self::CASE_PREFIX . 'C' . str_pad((string) $clientId, 3, '0', STR_PAD_LEFT) . '-' . $caseNumber,
                'patient_name' => $isModification ? $patientName . ' / تعديل' : $patientName,
                'pair' => $pair,
                'pair_key' => $pair['pair_key'],
                'units' => $this->unitsForPair($pair, $clientId, $caseIndex),
                'color' => $this->colorForCase($clientId, $caseIndex),
                'style' => $this->styleForPair($pair),
                'stage' => $stage,
                'created_at' => $createdAt,
                'initial_delivery_date' => $initialDeliveryDate,
                'actual_delivery_date' => $actualDeliveryDate,
                'prior_delivery_date' => $priorDeliveryDate,
                'invoice_date' => $isModification ? $priorDeliveryDate : $actualDeliveryDate,
                'invoice_status' => $invoiceStatus,
                'delivered_to_client' => $isCompleted ? 1 : 0,
                'discount_rate' => in_array($caseIndex, [2, 5], true) ? (0.10 + (($clientIndex + $caseIndex) % 2) * 0.05) : 0.0,
                'is_repeat' => $isRepeat,
                'repeat_of' => null,
                'is_modification' => $isModification,
                'note' => $isModification
                    ? 'أعيد فتح الحالة لإجراء تعديل بسيط بطلب العيادة.'
                    : ($isCompleted ? 'اكتملت الحالة بعد مراجعة الملاءمة واللون.' : 'الحالة قيد العمل ضمن جدول الإنتاج الحالي.'),
            ];
        }

        $repeatOf = 1;
        $blueprints[8]['case_id'] = $blueprints[$repeatOf]['case_id'] . '_REP';
        $blueprints[8]['patient_name'] = $blueprints[$repeatOf]['patient_name'] . ' / إعادة';
        $blueprints[8]['pair'] = $blueprints[$repeatOf]['pair'];
        $blueprints[8]['pair_key'] = $blueprints[$repeatOf]['pair_key'];
        $blueprints[8]['units'] = $blueprints[$repeatOf]['units'];
        $blueprints[8]['color'] = $blueprints[$repeatOf]['color'];
        $blueprints[8]['style'] = $blueprints[$repeatOf]['style'];
        $blueprints[8]['stage'] = $blueprints[$repeatOf]['pair']['stage_path'][0] ?? 1;
        $blueprints[8]['repeat_of'] = $repeatOf;
        $blueprints[8]['discount_rate'] = 0.0;

        return $blueprints;
    }

    private function selectPairsForClient(int $clientId, int $clientIndex, array $pairs): array
    {
        $wanted = min(count($pairs), 3 + $this->seededInt("pair-count-{$clientId}", 0, 3));
        $start = $this->seededInt("pair-start-{$clientId}-{$clientIndex}", 0, count($pairs) - 1);
        $rotated = [];
        for ($offset = 0; $offset < count($pairs); $offset++) {
            $rotated[] = $pairs[($start + $offset) % count($pairs)];
        }

        $selected = [];
        $seenPairs = [];
        $seenMaterials = [];
        $seenJobTypes = [];

        foreach ($rotated as $pair) {
            if (count($selected) >= $wanted) {
                break;
            }
            if (isset($seenMaterials[$pair['material_id']]) || isset($seenJobTypes[$pair['job_type_id']])) {
                continue;
            }

            $selected[] = $pair;
            $seenPairs[$pair['pair_key']] = true;
            $seenMaterials[$pair['material_id']] = true;
            $seenJobTypes[$pair['job_type_id']] = true;
        }

        foreach ($rotated as $pair) {
            if (count($selected) >= $wanted) {
                break;
            }
            if (isset($seenPairs[$pair['pair_key']])) {
                continue;
            }

            $selected[] = $pair;
            $seenPairs[$pair['pair_key']] = true;
            $seenMaterials[$pair['material_id']] = true;
            $seenJobTypes[$pair['job_type_id']] = true;
        }

        if (count($seenMaterials) < 3 || count($seenJobTypes) < 3) {
            throw new InvalidArgumentException(
                'The demo database needs at least three distinct compatible materials and job types.'
            );
        }

        return $selected;
    }

    private function compatiblePairs(string $connection): array
    {
        $select = [
            'mj.material_id',
            'mj.jobtype_id as job_type_id',
            'm.name as material_name',
            'm.price as material_price',
            'm.design',
            'm.mill',
            'm.print_3d',
            'm.sinter_furnace',
            'm.press_furnace',
            'm.finish',
            'm.qc',
            'm.delivery',
            'jt.name as job_type_name',
            'jt.teeth_or_jaw',
        ];

        if (Schema::connection($connection)->hasColumn('materials', 'metal_work')) {
            $select[] = 'm.metal_work';
        }

        $query = DB::connection($connection)
            ->table('material_jobtypes as mj')
            ->join('materials as m', 'm.id', '=', 'mj.material_id')
            ->join('job_types as jt', 'jt.id', '=', 'mj.jobtype_id')
            ->select($select)
            ->orderBy('m.id')
            ->orderBy('jt.id');

        foreach ([['material_jobtypes', 'mj'], ['materials', 'm'], ['job_types', 'jt']] as [$table, $alias]) {
            if (Schema::connection($connection)->hasColumn($table, 'deleted_at')) {
                $query->whereNull($alias . '.deleted_at');
            }
        }
        if (Schema::connection($connection)->hasColumn('materials', 'is_active')) {
            $query->where('m.is_active', '!=', 0);
        }

        $pairs = [];
        foreach ($query->get() as $row) {
            $pair = (array) $row;
            $pair['metal_work'] = $pair['metal_work'] ?? 0;
            $pair['pair_key'] = $pair['material_id'] . ':' . $pair['job_type_id'];
            $pair['stage_path'] = $this->stagePath($pair);
            if (count($pair['stage_path']) > 0) {
                $pairs[$pair['pair_key']] = $pair;
            }
        }

        return array_values($pairs);
    }

    private function stagePath(array $pair): array
    {
        $columns = [
            1 => 'design',
            2 => 'mill',
            3 => 'print_3d',
            4 => 'sinter_furnace',
            5 => 'press_furnace',
            9 => 'metal_work',
            6 => 'finish',
            7 => 'qc',
            8 => 'delivery',
        ];

        return array_values(array_filter(
            array_keys($columns),
            fn (int $stage): bool => ! empty($pair[$columns[$stage]])
        ));
    }

    private function insertCaseLogs(
        string $connection,
        int $caseId,
        int $userId,
        array $jobs,
        Carbon $createdAt
    ): void {
        $entries = [];

        foreach ($jobs as $job) {
            $path = $job['pair']['stage_path'] ?? $this->stagePath($job['pair']);
            $currentIndex = array_search($job['stage'], $path, true);

            foreach ($path as $pathIndex => $stage) {
                if ($job['stage'] !== -1 && ($currentIndex === false || $pathIndex > $currentIndex)) {
                    continue;
                }

                $isCompletion = $job['stage'] === -1 || $pathIndex < $currentIndex ? 1 : 0;
                $entries[$stage . ':' . $isCompletion] = [
                    'stage' => $stage,
                    'is_completion' => $isCompletion,
                    'order' => $pathIndex,
                ];
            }
        }

        usort($entries, function (array $left, array $right): int {
            return [$left['order'], $left['stage'], $left['is_completion']]
                <=> [$right['order'], $right['stage'], $right['is_completion']];
        });

        foreach (array_values($entries) as $entryIndex => $entry) {
            $loggedAt = $createdAt->copy()->addHours(($entryIndex + 1) * 4);
            DB::connection($connection)->table('case_logs')->insert(
                $this->filterColumns($connection, 'case_logs', [
                    'user_id' => $userId,
                    'case_id' => $caseId,
                    'stage' => $entry['stage'],
                    'is_completion' => $entry['is_completion'],
                    'created_at' => $loggedAt,
                    'updated_at' => $loggedAt,
                ])
            );
        }
    }

    private function insertFailureLog(
        string $connection,
        int $caseId,
        ?int $originalCaseId,
        int $failureType,
        int $causeId,
        int $userId,
        string $explanation,
        $oldDeliveryDate,
        $createdAt
    ): void {
        DB::connection($connection)->table('failure_logs')->insert(
            $this->filterColumns($connection, 'failure_logs', [
                'case_id' => $caseId,
                'original_case_id' => $originalCaseId,
                'failure_type' => $failureType,
                'cause_id' => $causeId,
                'explanation' => $explanation,
                'done_by' => $userId,
                'old_delivery_date' => $oldDeliveryDate,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])
        );
    }

    private function seedPayments(
        string $connection,
        int $clientId,
        int $clientIndex,
        int $userId,
        float $invoiceTotal,
        Carbon $today
    ): int {
        $count = $this->paymentCountForClient($clientId);
        $target = round(max(40, $invoiceTotal * (0.58 + ($clientIndex % 4) * 0.06)), 2);
        $weights = [];

        for ($paymentIndex = 0; $paymentIndex < $count; $paymentIndex++) {
            $weights[] = 2 + $this->seededInt("payment-weight-{$clientId}-{$paymentIndex}", 0, 8);
        }

        $weightTotal = array_sum($weights);
        $paid = 0.0;
        foreach ($weights as $paymentIndex => $weight) {
            $amount = $paymentIndex === $count - 1
                ? round($target - $paid, 2)
                : round($target * ($weight / $weightTotal), 2);
            $paid += $amount;
            $receivedAt = $today->copy()
                ->subDays(1 + $this->seededInt("payment-day-{$clientId}-{$paymentIndex}", 0, 40))
                ->setTime(9 + ($paymentIndex % 7), ($paymentIndex * 11) % 60);

            DB::connection($connection)->table('payments')->insert(
                $this->filterColumns($connection, 'payments', [
                    'amount' => max(0.01, $amount),
                    'notes' => 'دفعة تجريبية حديثة',
                    'doctor_id' => $clientId,
                    'collector' => $userId,
                    'received_by' => $userId,
                    'recieved_on' => $receivedAt,
                    'is_credit_note' => 0,
                    'additional_notes' => self::PAYMENT_PREFIX . 'C' . $clientId . '-' . ($paymentIndex + 1),
                    'created_at' => $receivedAt,
                    'updated_at' => $today,
                ])
            );
        }

        return $count;
    }

    private function paymentCountForClient(int $clientId): int
    {
        return 2 + $this->seededInt("payment-count-{$clientId}", 0, 5);
    }

    private function clearPreviousBatch(string $connection): void
    {
        $caseIds = DB::connection($connection)
            ->table('cases')
            ->where('case_id', 'like', self::CASE_PREFIX . '%')
            ->pluck('id');

        if ($caseIds->isNotEmpty()) {
            foreach (['case_tags', 'failure_logs', 'discounts', 'notes', 'case_logs', 'invoices', 'jobs'] as $table) {
                if (Schema::connection($connection)->hasTable($table)
                    && Schema::connection($connection)->hasColumn($table, 'case_id')) {
                    DB::connection($connection)->table($table)->whereIn('case_id', $caseIds)->delete();
                }
            }

            DB::connection($connection)->table('cases')->whereIn('id', $caseIds)->delete();
        }

        DB::connection($connection)
            ->table('payments')
            ->where('additional_notes', 'like', self::PAYMENT_PREFIX . '%')
            ->delete();
    }

    private function recalculateClientBalances(string $connection, array $clientIds): void
    {
        foreach ($clientIds as $clientId) {
            $invoiceQuery = DB::connection($connection)
                ->table('invoices')
                ->where('doctor_id', $clientId)
                ->where('status', 1);
            $paymentQuery = DB::connection($connection)
                ->table('payments')
                ->where('doctor_id', $clientId);

            if (Schema::connection($connection)->hasColumn('invoices', 'deleted_at')) {
                $invoiceQuery->whereNull('deleted_at');
            }
            if (Schema::connection($connection)->hasColumn('payments', 'deleted_at')) {
                $paymentQuery->whereNull('deleted_at');
            }

            DB::connection($connection)->table('clients')->where('id', $clientId)->update([
                'balance' => round((float) $invoiceQuery->sum('amount') - (float) $paymentQuery->sum('amount'), 2),
                'updated_at' => now(),
            ]);
        }
    }

    private function activeClients(string $connection)
    {
        $query = DB::connection($connection)->table('clients')->select('id', 'name')->orderBy('id');
        if (Schema::connection($connection)->hasColumn('clients', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }
        if (Schema::connection($connection)->hasColumn('clients', 'active')) {
            $query->where('active', '!=', 0);
        }

        return $query->get();
    }

    private function systemUserId(string $connection): int
    {
        $query = DB::connection($connection)->table('users')->orderBy('id');
        if (Schema::connection($connection)->hasColumn('users', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }
        if (Schema::connection($connection)->hasColumn('users', 'status')) {
            $query->where('status', '!=', 0);
        }

        $adminQuery = clone $query;
        $userId = Schema::connection($connection)->hasColumn('users', 'is_admin')
            ? $adminQuery->where('is_admin', 1)->value('id')
            : null;
        $userId = $userId ?? $query->value('id');

        if (! $userId) {
            throw new InvalidArgumentException('The demo database needs an active user for case and payment audit fields.');
        }

        return (int) $userId;
    }

    private function failureCauseId(string $connection): int
    {
        $query = DB::connection($connection)->table('failure_causes')->orderBy('id');
        if (Schema::connection($connection)->hasColumn('failure_causes', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }
        $causeId = $query->value('id');

        if (! $causeId) {
            throw new InvalidArgumentException('The demo database needs a failure cause before repeat and modification examples can be seeded.');
        }

        return (int) $causeId;
    }

    private function impressionTypeIds(string $connection): array
    {
        if (! Schema::connection($connection)->hasTable('impression_types')) {
            return [];
        }

        $query = DB::connection($connection)->table('impression_types')->orderBy('id');
        if (Schema::connection($connection)->hasColumn('impression_types', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query->pluck('id')->map(fn ($id): int => (int) $id)->all();
    }

    private function clientMaterialPrices(string $connection, int $clientId): array
    {
        if (! Schema::connection($connection)->hasTable('client_materials')) {
            return [];
        }

        return DB::connection($connection)
            ->table('client_materials')
            ->where('client_id', $clientId)
            ->pluck('price', 'material_id')
            ->map(fn ($price): float => (float) $price)
            ->all();
    }

    private function assertRequiredSchema(string $connection): void
    {
        $requiredTables = [
            'clients', 'cases', 'jobs', 'case_logs', 'notes', 'invoices', 'payments', 'users',
            'materials', 'job_types', 'material_jobtypes', 'discounts', 'failure_logs', 'failure_causes',
        ];

        foreach ($requiredTables as $table) {
            if (! Schema::connection($connection)->hasTable($table)) {
                throw new InvalidArgumentException("The demo database is missing the required {$table} table.");
            }
        }
    }

    private function filterColumns(string $connection, string $table, array $payload): array
    {
        $cacheKey = $connection . ':' . $table;
        if (! isset($this->columnCache[$cacheKey])) {
            $this->columnCache[$cacheKey] = array_flip(
                Schema::connection($connection)->getColumnListing($table)
            );
        }

        return array_intersect_key($payload, $this->columnCache[$cacheKey]);
    }

    private function patientName(int $clientIndex, int $caseIndex): string
    {
        $sequence = ($clientIndex * self::CASES_PER_CLIENT) + $caseIndex;

        return $this->patientFirstNames[$sequence % count($this->patientFirstNames)]
            . ' '
            . $this->patientLastNames[(($sequence * 7) + $clientIndex) % count($this->patientLastNames)];
    }

    private function unitsForPair(array $pair, int $clientId, int $caseIndex): string
    {
        if (! empty($pair['teeth_or_jaw'])) {
            return ($clientId + $caseIndex) % 2 === 0 ? 'upper' : 'lower';
        }

        $units = ['11', '12', '13', '14', '15', '21', '22', '23', '24', '25', '31', '32', '33', '34', '35', '36', '41', '42', '43', '44', '45', '46'];
        $start = $this->seededInt("units-{$clientId}-{$caseIndex}", 0, count($units) - 1);
        $count = 1 + $this->seededInt("unit-count-{$clientId}-{$caseIndex}", 0, 2);
        $selected = [];

        for ($offset = 0; $offset < $count; $offset++) {
            $selected[] = $units[($start + $offset) % count($units)];
        }

        return implode(',', $selected);
    }

    private function colorForCase(int $clientId, int $caseIndex): string
    {
        $colors = ['A1', 'A2', 'A3', 'B1', 'B2', 'C1'];

        return $colors[$this->seededInt("color-{$clientId}-{$caseIndex}", 0, count($colors) - 1)];
    }

    private function styleForPair(array $pair): string
    {
        $name = strtolower((string) $pair['job_type_name']);
        if (strpos($name, 'bridge') !== false) {
            return 'Bridge';
        }
        if (strpos($name, 'veneer') !== false) {
            return 'Layered';
        }
        if (strpos($name, 'guide') !== false || strpos($name, 'guard') !== false) {
            return 'Appliance';
        }

        return 'Single';
    }

    private function unitCount(string $units): int
    {
        if (in_array(strtolower($units), ['upper', 'lower'], true)) {
            return 1;
        }

        return max(1, count(array_filter(array_map('trim', explode(',', $units)))));
    }

    private function seededInt(string $key, int $minimum, int $maximum): int
    {
        if ($maximum <= $minimum) {
            return $minimum;
        }

        $value = (int) sprintf('%u', crc32($key));

        return $minimum + ($value % (($maximum - $minimum) + 1));
    }
}
