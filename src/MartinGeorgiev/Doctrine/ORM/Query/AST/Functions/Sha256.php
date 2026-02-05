<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL SHA256().
 *
 * Calculates the SHA256 hash of a string.
 *
 * @see https://www.postgresql.org/docs/17/functions-binarystring.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SHA256(e.data) FROM Entity e"
 */
class Sha256 extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('sha256(%s::bytea)');
        $this->addNodeMapping('StringPrimary');
    }
}
