<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Column implementation of Postgresql TO_JSONB().
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 * @since 1.8
 */
class ColumnToJsonb extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('to_jsonb(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
