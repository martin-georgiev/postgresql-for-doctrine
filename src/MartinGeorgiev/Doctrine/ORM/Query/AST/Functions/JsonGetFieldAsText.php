<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ->> operator.
 *
 * Extracts a JSON field as text.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with property name: "SELECT JSON_GET_FIELD_AS_TEXT(e.jsonData, 'name') FROM Entity e"
 * @example Using it in DQL with array index: "SELECT JSON_GET_FIELD_AS_TEXT(e.jsonData, 0) FROM Entity e"
 */
class JsonGetFieldAsText extends JsonGetField
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s ->> %s)');
    }
}
