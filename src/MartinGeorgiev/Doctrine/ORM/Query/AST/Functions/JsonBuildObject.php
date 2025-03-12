<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_BUILD_OBJECT().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.9.0
 */
class JsonBuildObject extends BaseVariadicFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_build_object(%s)');
    }
}
