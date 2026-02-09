<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_EACH_TEXT().
 *
 * Expands a JSONB object into key-value pairs with text values.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_EACH_TEXT(e.data) FROM Entity e"
 */
class JsonbEachText extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_each_text(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
