<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL COVAR_POP().
 *
 * Computes the population covariance between two sets of values.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT COVAR_POP(e.y, e.x) FROM Entity e"
 */
class CovarPop extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('covar_pop(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
