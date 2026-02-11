<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REVERSE() for bytea.
 *
 * Reverses the bytes in a string.
 *
 * @see https://www.postgresql.org/docs/18/functions-binarystring.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REVERSE_BYTES(e.data) FROM Entity e"
 */
class ReverseBytes extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('reverse(%s::bytea)');
        $this->addNodeMapping('StringPrimary');
    }
}
