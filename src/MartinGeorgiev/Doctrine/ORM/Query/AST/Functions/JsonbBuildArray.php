<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_BUILD_ARRAY().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbBuildArray extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'jsonb_build_array';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return PHP_INT_MAX;
    }
}
