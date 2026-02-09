<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_EXISTS().
 *
 * Supports basic form:
 * - json_exists(target_json, path_text)
 *
 * Note: The following variations are NOT supported due to DQL limitations:
 * - PASSING variables
 * - ON ERROR clause
 * - ON EMPTY clause
 * - WITH UNIQUE KEYS clause
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_EXISTS(e.jsonObject, '$.name') FROM Entity e"
 */
class JsonExists extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_exists(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
