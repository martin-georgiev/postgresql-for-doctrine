<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class CustomerTotal
{
    public function __construct(
        public string $name,
        public string $total,
    ) {}
}
