<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JUSTIFY_INTERVAL().
 *
 * Adjusts interval using justify_days and justify_hours, with additional sign adjustments.
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JUSTIFY_INTERVAL(e.interval) FROM Entity e"
 */
class JustifyInterval extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('justify_interval(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
