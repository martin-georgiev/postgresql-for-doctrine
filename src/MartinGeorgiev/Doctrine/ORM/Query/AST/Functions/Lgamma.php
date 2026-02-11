<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LGAMMA().
 *
 * Calculates the natural logarithm of the absolute value of the gamma function.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LGAMMA(e.value) FROM Entity e"
 */
class Lgamma extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('lgamma(%s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
