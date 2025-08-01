<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL json field retrieval as text, filtered by key (using ->>).
 *
 * Supports both string keys for object property access and integer indices for array element access:
 * - JSON_GET_FIELD_AS_TEXT(json_column, 'property_name') -> json_column->>'property_name'
 * - JSON_GET_FIELD_AS_TEXT(json_column, 0) -> json_column->>0
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonGetFieldAsText extends JsonGetField
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s ->> %s)');
    }
}
