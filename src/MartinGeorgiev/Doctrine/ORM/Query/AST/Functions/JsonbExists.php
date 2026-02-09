<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_EXISTS().
 *
 * Checks if a key exists in a JSONB object.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_EXISTS(e.jsonbObject, 'key') FROM Entity e"
 */
class JsonbExists extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_exists(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
