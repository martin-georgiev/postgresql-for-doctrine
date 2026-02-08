<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL OVERLAPS operator.
 *
 * Checks if left date interval overlaps with right interval.
 *
 * @see https://www.postgresql.org/docs/9.6/functions-datetime.html
 * @since 1.7
 *
 * @author Ramil Gallyamov <gallyamow@gmail.com>
 *
 * @example Using it in DQL: "SELECT e.id FROM Entity e WHERE (e.start_date, e.end_date) OVERLAPS ('2024-01-01', '2024-12-31')"
 */
class DateOverlaps extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s, %s) OVERLAPS (%s, %s)');
        $this->addNodeMapping('StringExpression');
        $this->addNodeMapping('StringExpression');
        $this->addNodeMapping('StringExpression');
        $this->addNodeMapping('StringExpression');
    }
}
