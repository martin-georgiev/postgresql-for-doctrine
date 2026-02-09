<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TO_CHAR().
 *
 * Converts a value to a string using a format string.
 *
 * @see https://www.postgresql.org/docs/17/functions-formatting.html
 * @since 3.3
 *
 * @author Andrei Karpilin <karpilin@gmail.com>
 *
 * @example Using it in DQL: "SELECT TO_CHAR(e.created_at, 'YYYY-MM-DD') FROM Entity e"
 */
class ToChar extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_char(%s, %s)');
        $this->addNodeMapping('ArithmeticFactor');
        $this->addNodeMapping('StringPrimary');
    }
}
