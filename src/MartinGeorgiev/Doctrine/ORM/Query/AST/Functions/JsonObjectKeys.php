<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_OBJECT_KEYS().
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonObjectKeys extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_object_keys(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
