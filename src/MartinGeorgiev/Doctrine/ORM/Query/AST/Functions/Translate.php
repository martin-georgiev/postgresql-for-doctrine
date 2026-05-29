<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TRANSLATE().
 *
 * Replaces each character in the string that matches a character in the from set with the corresponding character in the to set.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TRANSLATE(e.text1, 'abc', '123') FROM Entity e"
 */
class Translate extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('translate(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
