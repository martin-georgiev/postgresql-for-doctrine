<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CRC32C().
 *
 * @see https://www.postgresql.org/docs/18/functions-binarystring.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Crc32c extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('crc32c(%s::bytea)');
        $this->addNodeMapping('StringPrimary');
    }
}
