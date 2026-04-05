<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Base class for arithmetic functions that accept exactly two arguments (dyadic operations).
 *
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseDyadicArithmeticFunction extends BaseVariadicFunction
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
        return 2;
    }
}
