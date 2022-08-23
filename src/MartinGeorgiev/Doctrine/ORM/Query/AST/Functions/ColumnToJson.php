<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Column casting implementation of Postgresql TO_JSON().
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 * @since 1.8
 */
class ColumnToJson extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('to_json(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
