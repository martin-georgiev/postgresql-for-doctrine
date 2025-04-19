<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL Row Constructor expression.
 *
 * @see https://www.postgresql.org/docs/14/sql-expressions.html#SQL-SYNTAX-ROW-CONSTRUCTORS
 */
class Row extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['InParameter'];
    }

    protected function getFunctionName(): string
    {
        return 'ROW';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return PHP_INT_MAX; // No upper limit
    }
}
