<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL DATE_DIFF().
 *
 * Returns the number of specified units between two timestamps.
 *
 * @see https://www.postgresql.org/docs/16/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DATE_DIFF('day', e.startDate, e.endDate) FROM Entity e"
 */
class DateDiff extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('date_diff(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
