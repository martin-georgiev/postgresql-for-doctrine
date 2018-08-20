<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql LEAST()
 * @see https://www.postgresql.org/docs/9.4/static/functions-conditional.html#FUNCTIONS-GREATEST-LEAST
 *
 * @since 0.7
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Least extends Greatest
{
    /**
     * {@inheritDoc}
     */
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('least(%s)');
    }
}
