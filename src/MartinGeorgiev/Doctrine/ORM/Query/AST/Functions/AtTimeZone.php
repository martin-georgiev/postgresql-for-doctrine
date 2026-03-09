<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL AT TIME ZONE operator.
 *
 * Converts a timestamp to a different time zone.
 * When applied to a timestamp without time zone, it assumes the given timezone.
 * When applied to a timestamp with time zone, it converts to the given timezone.
 *
 * @see https://www.postgresql.org/docs/current/functions-datetime.html#FUNCTIONS-DATETIME-ZONECONVERT
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT AT_TIME_ZONE(e.createdAt, 'UTC') FROM Entity e"
 */
class AtTimeZone extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s AT TIME ZONE %s');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
