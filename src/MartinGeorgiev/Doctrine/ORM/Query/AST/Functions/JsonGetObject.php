<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL #> operator.
 *
 * Extracts a JSON object at the specified path.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with single path element: "SELECT JSON_GET_OBJECT(e.jsonData, '{address}') FROM Entity e"
 * @example Using it in DQL with nested path: "SELECT JSON_GET_OBJECT(e.jsonData, '{address,city}') FROM Entity e"
 */
class JsonGetObject extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s #> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
