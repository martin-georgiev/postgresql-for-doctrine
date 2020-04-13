<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_AGG().
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayAgg extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('array_agg(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
