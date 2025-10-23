<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CASEFOLD().
 *
 * @see https://www.postgresql.org/docs/18/functions-string.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Casefold extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('casefold(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
