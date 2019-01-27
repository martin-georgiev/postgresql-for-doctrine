<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql LEAST().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-conditional.html#FUNCTIONS-GREATEST-LEAST
 * @since 0.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Least extends BaseComparisonFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('least(%s)');
    }
}
