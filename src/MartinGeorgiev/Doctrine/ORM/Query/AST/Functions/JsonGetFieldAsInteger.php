<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL json field retrieval as integer, filtered by key (using ->> and type casting to BIGINT).
 *
 * Supports both string keys for object property access and integer indices for array element access:
 * - JSON_GET_FIELD_AS_INTEGER(json_column, 'property_name') -> CAST(json_column->>'property_name' as BIGINT)
 * - JSON_GET_FIELD_AS_INTEGER(json_column, 0) -> CAST(json_column->>0 as BIGINT)
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonGetFieldAsInteger extends JsonGetField
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('CAST(%s ->> %s as BIGINT)');
    }
}
