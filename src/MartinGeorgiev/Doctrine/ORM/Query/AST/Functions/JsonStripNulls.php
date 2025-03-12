<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_STRIP_NULLS().
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-array.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonStripNulls extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_strip_nulls(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
