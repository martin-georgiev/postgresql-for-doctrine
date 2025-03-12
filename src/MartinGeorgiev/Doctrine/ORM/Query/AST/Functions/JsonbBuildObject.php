<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSONB_BUILD_OBJECT().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.9.0
 */
class JsonbBuildObject extends BaseVariadicFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('jsonb_build_object(%s)');
    }
}
