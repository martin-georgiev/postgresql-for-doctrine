<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TS_RANK_CD().
 *
 * Ranks documents for query relevance using cover density ranking.
 *
 * @see https://www.postgresql.org/docs/18/textsearch-controls.html#TEXTSEARCH-RANKING
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TS_RANK_CD(TO_TSVECTOR(e.text), TO_TSQUERY(:query)) FROM Entity e"
 * @example Using it in DQL with normalization: "SELECT TS_RANK_CD(TO_TSVECTOR(e.text), TO_TSQUERY(:query), 1) FROM Entity e"
 */
class TsRankCd extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,StringPrimary,ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ts_rank_cd';
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
