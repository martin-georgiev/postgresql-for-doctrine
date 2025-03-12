<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_NDIMS().
 *
 * @see http://www.postgresql.org/docs/9.6/static/functions-array.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayNumberOfDimensions extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_ndims(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
