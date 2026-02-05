<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CASEFOLD().
 *
 * Converts a string to case-folded form for case-insensitive comparison.
 *
 * @see https://www.postgresql.org/docs/18/functions-string.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT CASEFOLD(e.name) FROM Entity e"
 */
class Casefold extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('casefold(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
