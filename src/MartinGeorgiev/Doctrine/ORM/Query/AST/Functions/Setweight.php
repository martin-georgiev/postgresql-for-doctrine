<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL SETWEIGHT().
 *
 * Assigns a weight label (A, B, C, or D) to each lexeme in a tsvector.
 *
 * @see https://www.postgresql.org/docs/18/textsearch-features.html#TEXTSEARCH-MANIPULATE-TSVECTOR
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SETWEIGHT(TO_TSVECTOR(e.title), 'A') FROM Entity e"
 * @example Using it in DQL with lexemes filter: "SELECT SETWEIGHT(TO_TSVECTOR(e.text), 'B', :lexemes) FROM Entity e"
 */
class Setweight extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'setweight';
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
