<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL @? operator.
 *
 * Checks if a JSON path expression returns a value.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT RETURNS_VALUE_FOR_JSON_VALUE(e.jsonObject, '$.name') FROM Entity e"
 */
class ReturnsValueForJsonValue extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s @?? %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
