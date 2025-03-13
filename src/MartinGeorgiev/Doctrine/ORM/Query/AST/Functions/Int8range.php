<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL INT8RANGE().
 *
 * @see https://www.postgresql.org/docs/17/rangetypes.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int8range extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('int8range(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
