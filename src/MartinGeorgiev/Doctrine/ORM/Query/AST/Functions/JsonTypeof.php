<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSON_TYPEOF().
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 1.8.0
 */
class JsonTypeof extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('json_typeof(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
