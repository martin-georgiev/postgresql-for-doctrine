<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_OBJECT_AGG().
 *
 * Aggregates key-value pairs into a JSON object.
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_OBJECT_AGG(e.key, e.value) FROM Entity e"
 */
class JsonObjectAgg extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_object_agg(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
