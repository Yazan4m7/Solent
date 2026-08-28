<?php

namespace App\Modules\Cases\Exceptions;

use RuntimeException;

class EmployeeProductionBoardException extends RuntimeException
{
    private string $errorCode;
    private int $status;
    private array $context;

    public function __construct(
        string $message,
        string $errorCode = 'employee_production_board_error',
        int $status = 409,
        array $context = []
    ) {
        parent::__construct($message);

        $this->errorCode = $errorCode;
        $this->status = $status;
        $this->context = $context;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function context(): array
    {
        return $this->context;
    }
}
