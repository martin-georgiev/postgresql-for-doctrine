<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TO_DATE().
 *
 * Converts a string to a date.
 *
 * @see https://www.postgresql.org/docs/17/functions-formatting.html
 * @since 3.3
 *
 * @author Andrei Karpilin <karpilin@gmail.com>
 *
 * @example Using it in DQL: "SELECT TO_DATE(e.date_str, 'YYYY-MM-DD') FROM Entity e"
 */
class ToDate extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_date(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
