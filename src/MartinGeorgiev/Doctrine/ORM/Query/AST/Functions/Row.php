<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL Row Constructor expression.
 *
 * @see https://www.postgresql.org/docs/14/sql-expressions.html#SQL-SYNTAX-ROW-CONSTRUCTORS
 */
class Row extends BaseVariadicFunction
{
    protected string $commonNodeMapping = 'InParameter';

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ROW(%s)');
    }

    protected function validateArguments(array $arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount === 0) {
            throw InvalidArgumentForVariadicFunctionException::atLeast('ROW', 1);
        }
    }
}
