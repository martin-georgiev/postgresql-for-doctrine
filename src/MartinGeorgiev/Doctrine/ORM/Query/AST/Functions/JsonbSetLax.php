<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_SET_LAX().
 *
 * Sets a value at a specified path in a JSONB object, with lax semantics.
 *
 * @see https://www.postgresql.org/docs/13/functions-json.html
 * @since 2.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_SET_LAX(e.jsonbObject, '{key}', '\"value\"') FROM Entity e"
 */
class JsonbSetLax extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_set_lax(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('NewValue');
    }
}
