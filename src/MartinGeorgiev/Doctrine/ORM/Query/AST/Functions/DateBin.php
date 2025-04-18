<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL DATE_BIN().
 *
 * Bins input into specified interval aligned with specified origin.
 *
 * @see https://www.postgresql.org/docs/14/functions-datetime.html
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DATE_BIN('15 minutes', e.createdAt, '2001-02-16 20:05:00') FROM Entity e"
 */
class DateBin extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('date_bin(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
