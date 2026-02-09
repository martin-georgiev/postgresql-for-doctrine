<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_ARRAY_ELEMENTS_TEXT().
 *
 * Expands a JSONB array into text elements.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_ARRAY_ELEMENTS_TEXT(e.data) FROM Entity e"
 */
class JsonbArrayElementsText extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_array_elements_text(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
