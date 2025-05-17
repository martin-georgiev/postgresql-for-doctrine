<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * @see https://www.postgresql.org/docs/17/rangetypes.html
 * @since 3.1
 *
 * @author Jan Klan <jan@klan.com.au>
 */
abstract class BaseRangeFunction extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'ArithmeticPrimary',
            'ArithmeticPrimary',
            'StringPrimary',
        ];
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
