<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL BIT_COUNT().
 *
 * Counts the bits set in a bytea or bit string value.
 *
 * @see https://www.postgresql.org/docs/18/functions-binarystring.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT BIT_COUNT(e.data) FROM Entity e"
 */
class BitCount extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('bit_count(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
