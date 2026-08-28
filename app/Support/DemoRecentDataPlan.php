<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use InvalidArgumentException;

class DemoRecentDataPlan
{
    public const CASE_PREFIX = 'SOLENT-RECENT-V1-';

    public const PAYMENT_MARKER = '[SOLENT-RECENT-V1]';

    public const CASES_PER_CLIENT = 10;

    private const FIRST_NAMES = [
        'آية', 'ليان', 'ريم', 'سلمى', 'ميرا', 'جود',
        'ليث', 'كرم', 'سيف', 'معاذ', 'نور', 'يارا',
    ];

    private const FAMILY_NAMES = [
        'السالم', 'الناصر', 'الكرمي', 'الرواشدة', 'العواملة', 'الخطيب',
        'الزعبي', 'المجالي', 'الشرايرة', 'العجلوني', 'الدباس', 'الخصاونة',
    ];

    private const COLORS = ['A1', 'A2', 'A3', 'A3.5', 'B1', 'B2', 'C1', 'D2'];

    /**
     * Build a deterministic, database-independent plan. Nothing is written here.
     */
    public function build(array $clients, array $compatiblePairs, CarbonInterface $anchor): array
    {
        if (count($compatiblePairs) < 3) {
            throw new InvalidArgumentException('At least three existing compatible material/job-type pairs are required.');
        }

        $anchorDate = CarbonImmutable::parse($anchor->toDateTimeString())->startOfDay();
        $pairs = $this->uniquePairs($compatiblePairs);
        if (count($pairs) < 3) {
            throw new InvalidArgumentException('At least three distinct compatible material/job-type pairs are required.');
        }

        $clientPlans = [];
        foreach (array_values($clients) as $clientIndex => $client) {
            $clientId = (int) ($client['id'] ?? 0);
            if ($clientId < 1) {
                throw new InvalidArgumentException('Every client in the recent demo plan must have an id.');
            }

            $selectedPairs = $this->pairsForClient($clientId, $pairs);
            $cases = $this->casesForClient($client, $clientIndex, $selectedPairs, $anchorDate);

            $clientPlans[] = [
                'client' => $client,
                'pairs' => $selectedPairs,
                'cases' => $cases,
                'payments' => $this->paymentsForClient($clientId, $clientIndex, $cases, $anchorDate),
            ];
        }

        return [
            'case_prefix' => self::CASE_PREFIX,
            'payment_marker' => self::PAYMENT_MARKER,
            'anchor' => $anchorDate->toDateTimeString(),
            'clients' => $clientPlans,
        ];
    }

    private function uniquePairs(array $pairs): array
    {
        $unique = [];
        foreach ($pairs as $pair) {
            $materialId = (int) ($pair['material_id'] ?? 0);
            $jobTypeId = (int) ($pair['jobtype_id'] ?? 0);
            if ($materialId < 1 || $jobTypeId < 1) {
                continue;
            }

            $key = $materialId . ':' . $jobTypeId;
            $pair['pair_key'] = $key;
            $unique[$key] = $pair;
        }

        ksort($unique);

        return array_values($unique);
    }

    private function pairsForClient(int $clientId, array $pairs): array
    {
        $maximum = min(6, count($pairs));
        $pairCount = 3 + ($this->stableNumber('pair-count:' . $clientId) % ($maximum - 2));
        $start = $this->stableNumber('pair-start:' . $clientId) % count($pairs);
        $selected = [];

        for ($offset = 0; $offset < $pairCount; $offset++) {
            $selected[] = $pairs[($start + $offset) % count($pairs)];
        }

        return $selected;
    }

    private function casesForClient(
        array $client,
        int $clientIndex,
        array $pairs,
        CarbonImmutable $anchor
    ): array {
        $clientId = (int) $client['id'];
        $cases = [];

        for ($caseNumber = 1; $caseNumber <= self::CASES_PER_CLIENT; $caseNumber++) {
            $pair = $pairs[($caseNumber - 1) % count($pairs)];
            $createdAt = $anchor
                ->subDays(30 - (($clientIndex * 3 + $caseNumber * 2) % 27))
                ->addHours(8 + (($clientIndex + $caseNumber) % 9));
            $patientName = $this->patientName($clientIndex, $caseNumber);
            $workflow = 'normal';

            if ($caseNumber === 8) {
                $workflow = 'modification';
                $patientName .= ' / تعديل';
            } elseif ($caseNumber === 9) {
                $workflow = 'repeat-origin';
            } elseif ($caseNumber === 10) {
                $workflow = 'repeat';
                $pair = $cases[8]['pair'];
                $patientName = $cases[8]['patient_name'] . ' / إعادة';
                $createdAt = CarbonImmutable::parse($cases[8]['created_at'])->addDays(2);
            }

            $units = $caseNumber === 10
                ? $cases[8]['unit_num']
                : $this->unitNumbers($clientIndex, $caseNumber, (int) ($pair['teeth_or_jaw'] ?? 0));
            $unitCount = count(array_filter(explode(',', $units), 'strlen'));
            $unitPrice = $this->unitPrice($client, $pair);
            $beforeDiscount = round($unitPrice * max(1, $unitCount), 2);
            $discountPercent = in_array($caseNumber, [2, 5, 8], true)
                ? 10 + (($clientIndex + $caseNumber) % 4) * 5
                : 0;
            $invoiceAmount = round($beforeDiscount * (100 - $discountPercent) / 100, 2);

            $stage = $this->normalStage($pair, $caseNumber);
            $actualDeliveryDate = null;
            $oldDeliveryDate = null;
            $delivered = false;
            if (in_array($caseNumber, [1, 2, 9], true)) {
                $stage = -1;
                $actualDeliveryDate = $createdAt->addDays(2)->toDateTimeString();
                $delivered = true;
            } elseif ($caseNumber === 8) {
                $stage = -1;
                $oldDeliveryDate = $createdAt->addDays(2)->toDateTimeString();
            } elseif ($caseNumber === 10) {
                $stage = $this->firstProductionStage($pair);
            }

            $jobs = [[
                'role' => $workflow === 'repeat' ? 'repeat' : 'original',
                'unit_num' => $units,
                'stage' => $stage,
                'is_repeat' => $workflow === 'repeat' ? 1 : 0,
                'is_modification' => 0,
                'original_case_number' => $workflow === 'repeat' ? 9 : null,
                'original_job_role' => $workflow === 'repeat' ? 'original' : null,
            ]];

            if ($workflow === 'modification') {
                $jobs[] = [
                    'role' => 'modification',
                    'unit_num' => $units,
                    'stage' => 6,
                    'is_repeat' => 0,
                    'is_modification' => 1,
                    'original_case_number' => 8,
                    'original_job_role' => 'original',
                ];
            }

            $invoice = null;
            if ($workflow !== 'repeat') {
                $invoiceStatus = $delivered || $workflow === 'modification' ? 1 : 0;
                $invoice = [
                    'status' => $invoiceStatus,
                    'amount_before_discount' => $beforeDiscount,
                    'amount' => $invoiceAmount,
                    'discount_percent' => $discountPercent,
                    'discount_title' => $discountPercent > 0
                        ? sprintf('Recent demo case discount (%d%%)', $discountPercent)
                        : null,
                    'date_applied' => $invoiceStatus === 1
                        ? ($actualDeliveryDate ?? $oldDeliveryDate)
                        : null,
                ];
            }

            $failure = null;
            if ($workflow === 'modification') {
                $failure = [
                    'failure_type' => 2,
                    'original_case_number' => 8,
                    'old_delivery_date' => $oldDeliveryDate,
                    'explanation' => self::CASE_PREFIX . ' seeded modification workflow',
                ];
            } elseif ($workflow === 'repeat') {
                $failure = [
                    'failure_type' => 1,
                    'original_case_number' => 9,
                    'old_delivery_date' => null,
                    'explanation' => self::CASE_PREFIX . ' seeded repeat workflow',
                ];
            }

            $cases[] = [
                'number' => $caseNumber,
                'case_id' => sprintf('%sC%06d-%02d', self::CASE_PREFIX, $clientId, $caseNumber),
                'patient_name' => $patientName,
                'workflow' => $workflow,
                'pair' => $pair,
                'unit_num' => $units,
                'color' => self::COLORS[($clientIndex + $caseNumber) % count(self::COLORS)],
                'style' => $unitCount > 1 ? 'Bridge' : 'Single',
                'created_at' => $createdAt->toDateTimeString(),
                'initial_delivery_date' => $createdAt->addDays(5 + ($caseNumber % 3))->toDateTimeString(),
                'actual_delivery_date' => $actualDeliveryDate,
                'old_delivery_date' => $oldDeliveryDate,
                'delivered_to_client' => $delivered ? 1 : 0,
                'locked' => in_array($workflow, ['modification', 'repeat-origin', 'repeat'], true) ? 1 : 0,
                'contains_modification' => $workflow === 'modification' ? 1 : 0,
                'first_case_number_if_repeated' => $workflow === 'repeat' ? 9 : null,
                'jobs' => $jobs,
                'invoice' => $invoice,
                'failure' => $failure,
            ];
        }

        return $cases;
    }

    private function paymentsForClient(
        int $clientId,
        int $clientIndex,
        array $cases,
        CarbonImmutable $anchor
    ): array {
        $count = 2 + ($this->stableNumber('payment-count:' . $clientId) % 6);
        $appliedTotal = array_reduce($cases, function (float $total, array $case): float {
            $invoice = $case['invoice'];

            return $total + ($invoice && $invoice['status'] === 1 ? (float) $invoice['amount'] : 0.0);
        }, 0.0);
        $paymentRatio = 0.45 + (($this->stableNumber('payment-ratio:' . $clientId) % 31) / 100);
        $paymentTotal = round($appliedTotal * $paymentRatio, 2);
        $weights = [];
        for ($number = 1; $number <= $count; $number++) {
            $weights[] = 75 + ($this->stableNumber("payment-weight:{$clientId}:{$number}") % 76);
        }

        $remaining = $paymentTotal;
        $weightTotal = array_sum($weights);
        $payments = [];
        foreach ($weights as $index => $weight) {
            $amount = $index === count($weights) - 1
                ? $remaining
                : round($paymentTotal * $weight / $weightTotal, 2);
            $remaining = round($remaining - $amount, 2);
            $number = $index + 1;

            $payments[] = [
                'amount' => max(0, $amount),
                'notes' => sprintf('%s Recent payment %d/%d', self::PAYMENT_MARKER, $number, $count),
                'additional_notes' => sprintf('%s-C%06d-P%02d', self::PAYMENT_MARKER, $clientId, $number),
                'created_at' => $anchor
                    ->subDays(2 + (($clientIndex * 5 + $number * 3) % 25))
                    ->addHours(9 + ($number % 7))
                    ->toDateTimeString(),
            ];
        }

        return $payments;
    }

    private function patientName(int $clientIndex, int $caseNumber): string
    {
        $sequence = $clientIndex * self::CASES_PER_CLIENT + $caseNumber - 1;

        return self::FIRST_NAMES[$sequence % count(self::FIRST_NAMES)] . ' '
            . self::FAMILY_NAMES[intdiv($sequence, count(self::FIRST_NAMES)) % count(self::FAMILY_NAMES)];
    }

    private function unitNumbers(int $clientIndex, int $caseNumber, int $teethOrJaw): string
    {
        if ($teethOrJaw === 1) {
            return ($clientIndex + $caseNumber) % 2 === 0 ? 'Upper' : 'Lower';
        }

        $unitCount = 1 + (($clientIndex + $caseNumber) % 3);
        $units = [];
        $firstTooth = 11 + (($clientIndex * 7 + $caseNumber * 3) % 28);
        for ($offset = 0; $offset < $unitCount; $offset++) {
            $tooth = $firstTooth + $offset;
            $units[] = (string) ($tooth > 48 ? 11 + ($tooth - 49) : $tooth);
        }

        return implode(',', $units);
    }

    private function unitPrice(array $client, array $pair): float
    {
        $materialId = (int) $pair['material_id'];
        $customPrices = (array) ($client['material_prices'] ?? []);
        $price = $customPrices[$materialId] ?? $pair['price'] ?? 0;

        return round(max(0, (float) $price), 2);
    }

    private function normalStage(array $pair, int $caseNumber): int
    {
        $stages = array_values(array_filter((array) ($pair['stages'] ?? []), function ($stage): bool {
            return is_numeric($stage) && (int) $stage > 0;
        }));
        if (count($stages) === 0) {
            $stages = [1, 6, 7, 8];
        }

        return (int) $stages[($caseNumber - 1) % count($stages)];
    }

    private function firstProductionStage(array $pair): int
    {
        $stage = $this->normalStage($pair, 1);

        return $stage === 8 ? 7 : $stage;
    }

    private function stableNumber(string $value): int
    {
        return (int) sprintf('%u', crc32($value));
    }
}
