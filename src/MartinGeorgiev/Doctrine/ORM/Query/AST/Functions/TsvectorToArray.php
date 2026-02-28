<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TSVECTOR_TO_ARRAY().
 *
 * Converts a tsvector to an array of its lexemes.
 *
 * @see https://www.postgresql.org/docs/18/functions-textsearch.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TSVECTOR_TO_ARRAY(TO_TSVECTOR(e.text)) FROM Entity e"
 */
class TsvectorToArray extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('tsvector_to_array(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
