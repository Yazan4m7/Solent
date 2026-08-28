<?php

namespace App\Modules\Cases\Support;

use InvalidArgumentException;

class ProductionStageMap
{
    private const DEFINITIONS = [
        1 => [
            'label' => 'Design',
            'permission' => 1,
            'route' => 'designer-cases-list',
            'start_log_stage' => 1,
            'complete_log_stage' => 1,
        ],
        2 => [
            'label' => 'Milling',
            'permission' => 2,
            'route' => 'Miller-cases-list',
            'start_log_stage' => 2.1,
            'complete_log_stage' => 2.3,
        ],
        3 => [
            'label' => '3D Printing',
            'permission' => 3,
            'route' => 'Print3D-cases-list',
            'start_log_stage' => 3.1,
            'complete_log_stage' => 3.3,
        ],
        4 => [
            'label' => 'Sintering',
            'permission' => 4,
            'route' => 'SinterFurnace-cases-list',
            'start_log_stage' => 4.1,
            'complete_log_stage' => 4.3,
        ],
        5 => [
            'label' => 'Pressing',
            'permission' => 5,
            'route' => 'PressFurnace-cases-list',
            'start_log_stage' => 5.1,
            'complete_log_stage' => 5.2,
        ],
        9 => [
            'label' => 'Metal Work',
            'permission' => 5,
            'route' => 'PressFurnace-cases-list',
            'start_log_stage' => 9.1,
            'complete_log_stage' => 9.2,
        ],
        6 => [
            'label' => 'Finishing & Build up',
            'permission' => 6,
            'route' => 'Finishing-cases-list',
            'start_log_stage' => 6,
            'complete_log_stage' => 6,
        ],
        7 => [
            'label' => 'Quality Control',
            'permission' => 7,
            'route' => 'QC-cases-list',
            'start_log_stage' => 7,
            'complete_log_stage' => 7,
        ],
        8 => [
            'label' => 'Delivery',
            'permission' => 8,
            'route' => 'Delivery-cases-list',
            'start_log_stage' => 8.2,
            'complete_log_stage' => 8.3,
        ],
    ];

    public function definitions(): array
    {
        return self::DEFINITIONS;
    }

    public function stages(): array
    {
        return array_keys(self::DEFINITIONS);
    }

    public function isValid(int $stage): bool
    {
        return isset(self::DEFINITIONS[$stage]);
    }

    public function permissionFor(int $stage): int
    {
        return (int) $this->definition($stage)['permission'];
    }

    public function label(int $stage): string
    {
        return (string) $this->definition($stage)['label'];
    }

    public function routeName(int $stage): string
    {
        return (string) $this->definition($stage)['route'];
    }

    public function startLogStage(int $stage): float
    {
        return (float) $this->definition($stage)['start_log_stage'];
    }

    public function completeLogStage(int $stage): float
    {
        return (float) $this->definition($stage)['complete_log_stage'];
    }

    public function activatesWhenStarted(int $stage): bool
    {
        return !in_array($stage, [2, 3, 8], true);
    }

    public function isDelivery(int $stage): bool
    {
        return $stage === 8;
    }

    private function definition(int $stage): array
    {
        if (!$this->isValid($stage)) {
            throw new InvalidArgumentException("Unknown production stage: {$stage}");
        }

        return self::DEFINITIONS[$stage];
    }
}
