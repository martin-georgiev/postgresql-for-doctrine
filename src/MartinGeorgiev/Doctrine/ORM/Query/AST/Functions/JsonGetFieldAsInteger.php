<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * DQL wrapper for PostgreSQL ->> operator with BIGINT casting.
 *
 * Extracts a JSON field as text and casts it to BIGINT.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with property name: "SELECT JSON_GET_FIELD_AS_INTEGER(e.jsonData, 'age') FROM Entity e"
 * @example Using it in DQL with array index: "SELECT JSON_GET_FIELD_AS_INTEGER(e.jsonData, 0) FROM Entity e"
 */
class JsonGetFieldAsInteger extends JsonGetField
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('CAST(%s ->> %s as BIGINT)');
    }
}
