<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Two same-length patterns whose second slots take the same node type,
 * so a second argument the first pattern rejects is one the second pattern rejects too.
 */
class TestPatternRewindFunction extends BaseVariadicFunction
{
    protected function getFunctionName(): string
    {
        return 'test_pattern_rewind';
    }

    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary',
            'SimpleArithmeticExpression,StringPrimary',
        ];
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
