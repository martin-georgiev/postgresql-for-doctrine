<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL GAMMA().
 *
 * Calculates the gamma function of a number.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GAMMA(e.value) FROM Entity e"
 */
class Gamma extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('gamma(%s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
