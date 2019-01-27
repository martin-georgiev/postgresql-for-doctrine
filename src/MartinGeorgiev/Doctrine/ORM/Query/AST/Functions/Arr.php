<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY[].
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Arr extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('ARRAY[%s]');
        $this->addNodeMapping('StringPrimary');
    }
}
