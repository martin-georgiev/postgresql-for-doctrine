<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ROW_TO_JSON().
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class RowToJson extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('row_to_json(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
