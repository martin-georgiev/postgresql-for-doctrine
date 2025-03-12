<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_ARRAY_LENGTH().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbArrayLength extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_array_length(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
