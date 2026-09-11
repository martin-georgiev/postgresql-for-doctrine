<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * A NewValue-mapped argument, used to exercise BaseVariadicFunction with a literal NULL —
 * NewValue() is the only parser method it calls that returns PHP null instead of a Node.
 */
class TestNewValueArgumentFunction extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,NewValue'];
    }

    protected function getFunctionName(): string
    {
        return 'test_new_value_argument';
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
