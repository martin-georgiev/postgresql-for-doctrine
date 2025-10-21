<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LGAMMA().
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Lgamma extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('lgamma(%s)');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
