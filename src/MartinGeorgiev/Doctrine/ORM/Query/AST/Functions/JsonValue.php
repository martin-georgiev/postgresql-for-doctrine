<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_VALUE().
 *
 * Supports basic form:
 * - json_value(target_json, path_text)
 *
 * Note: The following variations are NOT supported due to DQL limitations:
 * - PASSING variables
 * - RETURNING type
 * - DEFAULT clause
 * - ON ERROR clause
 * - ON EMPTY clause
 * - ERROR/NULL/DEFAULT ON ERROR
 * - ERROR/NULL/DEFAULT ON EMPTY
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonValue extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_value(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
