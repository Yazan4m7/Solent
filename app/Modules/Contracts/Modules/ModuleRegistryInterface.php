<?php

namespace App\Modules\Contracts\Modules;

use App\Modules\Support\ModuleMetadata;

interface ModuleRegistryInterface
{
    /**
     * @return ModuleMetadata[]
     */
    public function all(): array;
}
