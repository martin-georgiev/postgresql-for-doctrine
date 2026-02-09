<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL INT8RANGE().
 *
 * Creates a bigint range.
 *
 * @see https://www.postgresql.org/docs/17/rangetypes.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT INT8RANGE(1, 10) FROM Entity e"
 */
class Int8range extends BaseVariadicFunction
{
    protected function getFunctionName(): string
    {
        return 'int8range';
    }

    /**
     * @return array<string>
     */
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
