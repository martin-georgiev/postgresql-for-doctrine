<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL EXTRACT().
 *
 * Extracts a field from a date or timestamp.
 *
 * @see https://www.postgresql.org/docs/14/functions-datetime.html
 * @since 2.1
 *
 * @author keithbrink <keith.brink@gmail.com>
 *
 * @example Using it in DQL: "SELECT DATE_EXTRACT('year', e.created_at) FROM Entity e"
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
