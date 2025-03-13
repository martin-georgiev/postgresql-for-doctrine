<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL LEAST().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-conditional.html#FUNCTIONS-GREATEST-LEAST
 * @since 0.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Least extends BaseComparisonFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('least(%s)');
    }

    protected function validateArguments(array $arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            throw InvalidArgumentForVariadicFunctionException::atLeast('least', 2);
        }
    }
}
