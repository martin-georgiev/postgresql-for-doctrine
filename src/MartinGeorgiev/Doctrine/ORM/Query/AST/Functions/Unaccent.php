<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL UNACCENT.
 *
 * Removes accents from characters in a string.
 *
 * @see https://www.postgresql.org/docs/17/unaccent.html
 * @since 1.5
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 *
 * @example Using it in DQL: "SELECT UNACCENT(e.name) FROM Entity e"
 */
class Unaccent extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'unaccent';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
