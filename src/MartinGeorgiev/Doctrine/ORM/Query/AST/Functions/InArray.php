<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL STRING_TO_ARRAY().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InArray extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s = ANY(%s)');
        $this->addNodeMapping('InputParameter');
        $this->addNodeMapping('StringPrimary');
    }
}
