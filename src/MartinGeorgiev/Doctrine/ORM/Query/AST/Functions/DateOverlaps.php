<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL check if left date interval overlaps with right interval.
 *
 * @see https://www.postgresql.org/docs/9.6/functions-datetime.html
 * @since 1.7.0
 *
 * @author Ramil Gallyamov <gallyamow@gmail.com>
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
