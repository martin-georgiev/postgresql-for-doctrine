<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CRC32().
 *
 * Calculates the CRC32 checksum of a string.
 *
 * @see https://www.postgresql.org/docs/18/functions-binarystring.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT CRC32(e.data) FROM Entity e"
 */
class Crc32 extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('crc32(%s::bytea)');
        $this->addNodeMapping('StringPrimary');
    }
}
