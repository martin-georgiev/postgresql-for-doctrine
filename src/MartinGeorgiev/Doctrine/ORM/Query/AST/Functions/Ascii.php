<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ASCII().
 *
 * Returns the numeric code of the first character of the argument.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ASCII(e.text1) FROM Entity e"
 */
class Ascii extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ascii(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
