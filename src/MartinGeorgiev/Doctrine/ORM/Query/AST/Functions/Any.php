<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ANY().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-subquery.html#FUNCTIONS-SUBQUERY-ANY-SOME
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Any extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('ANY(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
