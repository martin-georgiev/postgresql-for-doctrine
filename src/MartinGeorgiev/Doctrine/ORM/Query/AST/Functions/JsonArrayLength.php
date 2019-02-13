<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSON_ARRAY_LENGTH().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonArrayLength extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('json_array_length(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
