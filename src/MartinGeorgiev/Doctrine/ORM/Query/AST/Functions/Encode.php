<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ENCODE().
 *
 * Encodes binary data into a textual representation.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ENCODE(e.text1, 'base64') FROM Entity e"
 */
class Encode extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('encode(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
