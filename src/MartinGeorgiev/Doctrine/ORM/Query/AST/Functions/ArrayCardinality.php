<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CARDINALITY().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayCardinality extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('cardinality(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
