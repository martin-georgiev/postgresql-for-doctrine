<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_SCALAR().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonScalar extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_scalar(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
