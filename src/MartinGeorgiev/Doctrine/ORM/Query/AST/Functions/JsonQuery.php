<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_QUERY().
 *
 * Supports basic form:
 * - json_query(target_json, path_text)
 *
 * Note: The following variations are NOT supported due to DQL limitations:
 * - PASSING variables
 * - RETURNING type
 * - WRAPPER clause
 * - ON ERROR clause
 * - ON EMPTY clause
 * - WITH WRAPPER clause
 * - WITH CONDITIONAL WRAPPER clause
 * - EMPTY/NULL ON EMPTY
 * - ERROR/NULL ON ERROR
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonQuery extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_query(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
