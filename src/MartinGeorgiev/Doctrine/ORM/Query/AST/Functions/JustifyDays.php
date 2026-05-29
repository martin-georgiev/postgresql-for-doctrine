<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JUSTIFY_DAYS().
 *
 * Adjusts interval so 30-day time periods are represented as months.
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JUSTIFY_DAYS(e.interval) FROM Entity e"
 */
class JustifyDays extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('justify_days(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
