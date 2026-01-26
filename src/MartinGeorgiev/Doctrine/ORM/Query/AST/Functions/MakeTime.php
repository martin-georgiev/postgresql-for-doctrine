<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL MAKE_TIME().
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT MAKE_TIME(10, 30, 0) FROM Entity e"
 */
class MakeTime extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('make_time(%s, %s, %s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
