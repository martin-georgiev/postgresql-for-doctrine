<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_ARRAY_ELEMENTS().
 *
 * Expands a JSONB array into individual elements.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_ARRAY_ELEMENTS(e.data) FROM Entity e"
 */
class JsonbArrayElements extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_array_elements(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
