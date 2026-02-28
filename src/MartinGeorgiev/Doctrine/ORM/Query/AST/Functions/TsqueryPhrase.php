<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TSQUERY_PHRASE().
 *
 * Combines two tsquery values into a phrase query, optionally specifying the distance between them.
 *
 * @see https://www.postgresql.org/docs/18/functions-textsearch.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TSQUERY_PHRASE(TO_TSQUERY('cat'), TO_TSQUERY('rat')) FROM Entity e"
 * @example Using it in DQL with distance: "SELECT TSQUERY_PHRASE(TO_TSQUERY('cat'), TO_TSQUERY('rat'), 2) FROM Entity e"
 */
class TsqueryPhrase extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,StringPrimary,ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'tsquery_phrase';
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
