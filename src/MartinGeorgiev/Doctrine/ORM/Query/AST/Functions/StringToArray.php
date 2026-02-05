<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL STRING_TO_ARRAY().
 *
 * Splits a string into an array using a delimiter.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT STRING_TO_ARRAY(e.text, ',') FROM Entity e"
 */
class StringToArray extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('string_to_array(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
