<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JUSTIFY_HOURS().
 *
 * Adjusts interval so 24-hour time periods are represented as days.
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JUSTIFY_HOURS(e.interval) FROM Entity e"
 */
class JustifyHours extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('justify_hours(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
