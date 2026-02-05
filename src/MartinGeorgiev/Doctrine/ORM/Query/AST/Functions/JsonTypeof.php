<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_TYPEOF().
 *
 * Returns the type of a JSON value.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 1.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_TYPEOF(e.data) FROM Entity e"
 */
class JsonTypeof extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_typeof(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
