<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL DIFFERENCE().
 *
 * Converts two strings to their Soundex codes and then reports the number of matching code positions.
 * Since Soundex codes have four characters, the result ranges from zero to four,
 * with zero being no match and four being an exact match.
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DIFFERENCE(e.name1, e.name2) FROM Entity e"
 */
class Difference extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('difference(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
