<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_TO_TSVECTOR().
 *
 * Converts an array of text to a tsvector using each element as a lexeme.
 *
 * @see https://www.postgresql.org/docs/18/functions-textsearch.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_TO_TSVECTOR(ARRAY('cat', 'rat')) FROM Entity e"
 */
class ArrayToTsvector extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_to_tsvector(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
