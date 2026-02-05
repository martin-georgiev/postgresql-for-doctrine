<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_OBJECT_KEYS().
 *
 * Returns the keys of a JSON object.
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_OBJECT_KEYS(e.data) FROM Entity e"
 */
class JsonObjectKeys extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_object_keys(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
