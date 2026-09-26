<?php

declare(strict_types=1);

namespace MartinGeorgiev\Docs;

final readonly class DqlExample
{
    public function __construct(
        public string $fileAndLine,
        public string $dql,
        public ?string $documentedFunctionClass = null,
    ) {}
}
