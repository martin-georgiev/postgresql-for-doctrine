<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_SERIALIZE().
 *
 * Supports basic form:
 * - json_serialize(expression)
 *
 * Note: The following variations are NOT supported due to DQL limitations:
 * - RETURNING type
 * - FORMAT JSON clause
 * - ENCODING clause
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonSerialize extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_serialize(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
