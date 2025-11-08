<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL GAMMA().
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Gamma extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('gamma(%s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
