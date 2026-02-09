<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL SOUNDEX().
 *
 * Converts a string to its Soundex code.
 * The Soundex system is a method of matching similar-sounding names by converting them to the same code.
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SOUNDEX(e.name) FROM Entity e"
 */
class Soundex extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('soundex(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}

