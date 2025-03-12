<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql Row Constructor expression.
 *
 * @see https://www.postgresql.org/docs/14/sql-expressions.html#SQL-SYNTAX-ROW-CONSTRUCTORS
 */
class Row extends BaseVariadicFunction
{
    protected string $commonNodeMapping = 'InParameter';

    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('ROW(%s)');
    }
}
