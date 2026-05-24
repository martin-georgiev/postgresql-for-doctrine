<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CHAR_LENGTH().
 *
 * Returns the number of characters in the string.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT CHAR_LENGTH(e.text1) FROM Entity e"
 */
class CharLength extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('char_length(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
