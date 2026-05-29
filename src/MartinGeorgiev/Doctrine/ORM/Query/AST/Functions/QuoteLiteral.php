<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL QUOTE_LITERAL().
 *
 * Returns the given string suitably quoted to be used as a string literal in an SQL statement string.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT QUOTE_LITERAL(e.text1) FROM Entity e"
 */
class QuoteLiteral extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('quote_literal(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
