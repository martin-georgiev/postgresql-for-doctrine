<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REVERSE().
 *
 * Reverses the characters in a string.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REVERSE(e.text1) FROM Entity e"
 */
class Reverse extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('reverse(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
