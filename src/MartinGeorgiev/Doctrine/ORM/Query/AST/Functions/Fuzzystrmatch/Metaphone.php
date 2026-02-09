<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL METAPHONE().
 *
 * Calculates the metaphone code of an input string.
 * Metaphone, like Soundex, is based on the idea of constructing a representative code for an input string.
 * Two strings are then deemed similar if they have the same codes.
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT METAPHONE(e.name, 4) FROM Entity e"
 */
class Metaphone extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('metaphone(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}

