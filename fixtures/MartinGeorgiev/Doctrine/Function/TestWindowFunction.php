<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseArithmeticFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WindowFunction;

/**
 * A window-only function, so OVER can be shown to accept one before any real window function ships.
 */
class TestWindowFunction extends BaseArithmeticFunction implements WindowFunction
{
    protected function getFunctionName(): string
    {
        return 'test_window';
    }

    protected function getMaxArgumentCount(): int
    {
        return 0;
    }

    protected function getMinArgumentCount(): int
    {
        return 0;
    }
}
