<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL STRIP().
 *
 * Removes position and weight information from a tsvector, leaving only the lexemes.
 *
 * @see https://www.postgresql.org/docs/18/functions-textsearch.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT STRIP(TO_TSVECTOR(e.text)) FROM Entity e"
 */
class Strip extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('strip(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
