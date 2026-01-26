<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_COMPACT().
 *
 * @see https://www.postgresql.org/docs/17/functions-array.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayCompact extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_compact(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}

