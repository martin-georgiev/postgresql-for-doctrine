<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * A concrete implementation of BaseVariadicFunction for testing purposes.
 */
class TestVariadicFunction extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'test_function';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 10;
    }
}
