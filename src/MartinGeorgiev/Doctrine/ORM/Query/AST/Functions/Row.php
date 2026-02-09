<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ROW() constructor expression.
 *
 * Constructs a row value from arguments.
 *
 * @see https://www.postgresql.org/docs/14/sql-expressions.html#SQL-SYNTAX-ROW-CONSTRUCTORS
 * @since 2.8
 *
 * @author Bruno Zanchettin
 *
 * @example Using it in DQL: "SELECT ROW(e.field1, e.field2) FROM Entity e"
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
