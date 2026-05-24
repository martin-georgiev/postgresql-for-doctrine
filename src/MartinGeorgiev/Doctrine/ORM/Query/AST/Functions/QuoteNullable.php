<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL QUOTE_NULLABLE().
 *
 * Returns the given string suitably quoted to be used as a string literal in an SQL statement string, or NULL if the argument is null.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT QUOTE_NULLABLE(e.text1) FROM Entity e"
 */
class QuoteNullable extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('quote_nullable(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
