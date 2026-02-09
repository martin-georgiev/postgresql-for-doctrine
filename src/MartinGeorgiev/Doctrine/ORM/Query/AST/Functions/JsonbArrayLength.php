<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_ARRAY_LENGTH().
 *
 * Returns the length of a JSONB array.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_ARRAY_LENGTH(e.data) FROM Entity e"
 */
class JsonbArrayLength extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_array_length(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
