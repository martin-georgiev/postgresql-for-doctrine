<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TSRANGE().
 *
 * Creates a timestamp range.
 *
 * @see https://www.postgresql.org/docs/17/rangetypes.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TSRANGE('2024-01-01', '2024-12-31') FROM Entity e"
 */
class Tsrange extends BaseVariadicFunction
{
    protected function getFunctionName(): string
    {
        return 'tsrange';
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
