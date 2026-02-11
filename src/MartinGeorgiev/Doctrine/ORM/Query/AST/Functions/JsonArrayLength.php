<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_ARRAY_LENGTH().
 *
 * Returns the length of a JSON array.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_ARRAY_LENGTH(e.data) FROM Entity e"
 */
class JsonArrayLength extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_array_length(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
