<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_DIMS().
 *
 * @see http://www.postgresql.org/docs/9.6/static/functions-array.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayDimensions extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('array_dims(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
