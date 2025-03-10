<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql DATERANGE().
 *
 * @see https://www.postgresql.org/docs/17/functions-range.html
 * @since 2.9.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Daterange extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('daterange(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
