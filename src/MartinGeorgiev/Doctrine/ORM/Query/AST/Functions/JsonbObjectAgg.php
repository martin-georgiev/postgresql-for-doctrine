<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_OBJECT_AGG().
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbObjectAgg extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_object_agg(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
