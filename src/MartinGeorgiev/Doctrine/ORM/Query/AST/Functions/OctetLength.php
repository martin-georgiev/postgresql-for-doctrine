<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL OCTET_LENGTH().
 *
 * Returns the number of bytes in the string.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OCTET_LENGTH(e.text1) FROM Entity e"
 */
class OctetLength extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('octet_length(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
