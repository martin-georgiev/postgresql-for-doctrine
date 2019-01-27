<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_PREPEND().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayPrepend extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('array_prepend(%s, %s)');
        $this->addNodeMapping('Literal');
        $this->addNodeMapping('StringPrimary');
    }
}
