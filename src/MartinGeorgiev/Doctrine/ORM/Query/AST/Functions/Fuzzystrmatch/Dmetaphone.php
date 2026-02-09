<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL DMETAPHONE().
 *
 * Computes the primary "sounds like" string for a given input string using the Double Metaphone system.
 * The Double Metaphone system computes two "sounds like" strings for a given input string —
 * a "primary" and an "alternate".
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DMETAPHONE(e.name) FROM Entity e"
 */
class Dmetaphone extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('dmetaphone(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
