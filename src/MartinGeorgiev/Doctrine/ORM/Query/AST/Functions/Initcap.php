<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL INITCAP().
 *
 * Converts the first letter of each word to upper case and the rest to lower case.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT INITCAP(e.text1) FROM Entity e"
 */
class Initcap extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('initcap(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
