<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ROW_NUMBER() window function.
 *
 * Returns the number of the current row within its partition, counting from 1.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ROW_NUMBER_OVER(PARTITION BY e.category ORDER BY e.createdAt DESC) FROM Entity e"
 */
class RowNumberOver extends BaseWindowFunction
{
    protected function getFunctionName(): string
    {
        return 'row_number';
    }
}
