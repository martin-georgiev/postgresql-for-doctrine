<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * @since 1.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseComparisonFunction extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['SimpleArithmeticExpression'];
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return PHP_INT_MAX; // No hard limit apart the PHP internals
    }
}
