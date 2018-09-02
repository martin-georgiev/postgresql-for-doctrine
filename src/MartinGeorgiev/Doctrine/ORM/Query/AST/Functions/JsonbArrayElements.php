<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSONB_ARRAY_ELEMENTS()
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbArrayElements extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('jsonb_array_elements(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
