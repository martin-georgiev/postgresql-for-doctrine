<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL extract subfields such as year or hour
 * from date/time values.
 *
 * @see https://www.postgresql.org/docs/14/functions-datetime.html
 * @since 2.1
 *
 * @author keithbrink <keith.brink@gmail.com>
 */
class DateExtract extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('EXTRACT(%s FROM %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
