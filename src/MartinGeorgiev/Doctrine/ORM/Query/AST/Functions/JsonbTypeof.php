<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_TYPEOF().
 *
 * Returns the type of a JSONB value.
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_TYPEOF(e.jsonbData) FROM Entity e"
 */
class JsonbTypeof extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_typeof(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
