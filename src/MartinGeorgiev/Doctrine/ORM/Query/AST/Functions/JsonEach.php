<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_EACH().
 *
 * Expands a JSON object into key-value pairs.
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_EACH(e.data) FROM Entity e"
 */
class JsonEach extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_each(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
