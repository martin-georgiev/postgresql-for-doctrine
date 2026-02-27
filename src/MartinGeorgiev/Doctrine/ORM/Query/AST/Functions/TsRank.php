<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TS_RANK().
 *
 * Ranks documents for query relevance based on lexeme frequency.
 *
 * @see https://www.postgresql.org/docs/18/textsearch-controls.html#TEXTSEARCH-RANKING
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TS_RANK(TO_TSVECTOR(e.text), TO_TSQUERY(:query)) FROM Entity e"
 * @example Using it in DQL with normalization: "SELECT TS_RANK(TO_TSVECTOR(e.text), TO_TSQUERY(:query), 1) FROM Entity e"
 * @example Using it in DQL with weights: "SELECT TS_RANK('{0.1,0.2,0.4,1.0}', TO_TSVECTOR(e.text), TO_TSQUERY(:query)) FROM Entity e"
 * @example Using it in DQL with weights and normalization: "SELECT TS_RANK('{0.1,0.2,0.4,1.0}', TO_TSVECTOR(e.text), TO_TSQUERY(:query), 1) FROM Entity e"
 */
class TsRank extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ts_rank';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
