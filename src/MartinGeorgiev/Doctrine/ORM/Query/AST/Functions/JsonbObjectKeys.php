<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_OBJECT_KEYS().
 *
 * Returns the keys of a JSONB object.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_OBJECT_KEYS(e.data) FROM Entity e"
 */
class JsonbObjectKeys extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_object_keys(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
