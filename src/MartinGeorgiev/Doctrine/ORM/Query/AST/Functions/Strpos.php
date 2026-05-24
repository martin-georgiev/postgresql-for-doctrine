<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL STRPOS().
 *
 * Returns the location of a specified substring within a string.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT STRPOS(e.text1, 'search') FROM Entity e"
 */
class Strpos extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('strpos(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
