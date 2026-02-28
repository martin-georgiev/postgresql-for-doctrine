<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PLAINTO_TSQUERY().
 *
 * Converts plain text to a tsquery, treating all words as required terms (no operator syntax).
 *
 * @see https://www.postgresql.org/docs/18/textsearch-controls.html#TEXTSEARCH-PARSING-QUERIES
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT PLAINTO_TSQUERY(e.text) FROM Entity e"
 * @example Using it in DQL with config: "SELECT PLAINTO_TSQUERY('english', e.text) FROM Entity e"
 */
class PlaintoTsquery extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'plainto_tsquery';
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
