<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_TO_ARRAY().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbToArray extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_to_array(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
