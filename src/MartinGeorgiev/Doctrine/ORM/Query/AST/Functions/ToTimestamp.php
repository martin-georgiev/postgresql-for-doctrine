<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TO_TIMESTAMP().
 *
 * Converts a string to a timestamp.
 *
 * @see https://www.postgresql.org/docs/17/functions-formatting.html
 * @since 3.3
 *
 * @author Andrei Karpilin <karpilin@gmail.com>
 *
 * @example Using it in DQL: "SELECT TO_TIMESTAMP(e.text, 'YYYY-MM-DD HH24:MI:SS') FROM Entity e"
 */
class ToTimestamp extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_timestamp(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
