<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseWindowFunction;

/**
 * Two leading arguments with the second optional, so a comma separates arguments and a miscount reports a range.
 */
class TestWindowFunction extends BaseWindowFunction
{
    protected function customizeFunction(): void
    {
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }

    protected function getFunctionName(): string
    {
        return 'test_window';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }
}
