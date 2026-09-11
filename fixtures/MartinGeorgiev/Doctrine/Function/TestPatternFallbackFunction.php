<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Two same-length patterns that diverge on the second argument's type,
 * so the first pattern only fails once it is already parsing that later argument.
 */
class TestPatternFallbackFunction extends BaseVariadicFunction
{
    protected function getFunctionName(): string
    {
        return 'test_pattern_fallback';
    }

    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary',
            'ArithmeticPrimary,ArithmeticPrimary',
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
