<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ISFINITE().
 *
 * Tests whether a date, timestamp, or interval value is finite.
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ISFINITE(e.date1) FROM Entity e"
 */
class Isfinite extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('isfinite(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
