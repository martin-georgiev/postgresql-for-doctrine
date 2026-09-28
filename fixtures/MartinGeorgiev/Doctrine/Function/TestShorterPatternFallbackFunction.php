<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * A two-slot pattern followed by a one-slot pattern that is only meant for a single argument.
 */
class TestShorterPatternFallbackFunction extends BaseVariadicFunction
{
    protected function getFunctionName(): string
    {
        return 'test_shorter_pattern_fallback';
    }

    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary',
            'SimpleArithmeticExpression',
        ];
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
