<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL DATE_PART().
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DATE_PART('year', e.datetime1) FROM Entity e"
 */
class DatePart extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('date_part(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}

