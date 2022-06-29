<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql extract subfields such as year or hour
 * from date/time values.
 *
 * @see https://www.postgresql.org/docs/14/functions-datetime.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateExtract extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('EXTRACT(%s FROM %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
