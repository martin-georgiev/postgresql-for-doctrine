<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL DMETAPHONE_ALT().
 *
 * Computes the alternate "sounds like" string for a given input string using the Double Metaphone system.
 * The Double Metaphone system computes two "sounds like" strings for a given input string —
 * a "primary" and an "alternate". In most cases they are the same, but for non-English names
 * especially they can be a bit different, depending on pronunciation.
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DMETAPHONE_ALT(e.name) FROM Entity e"
 */
class DmetaphoneAlt extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('dmetaphone_alt(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
