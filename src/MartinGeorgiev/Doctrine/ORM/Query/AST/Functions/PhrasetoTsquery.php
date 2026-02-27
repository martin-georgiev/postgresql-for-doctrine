<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PHRASETO_TSQUERY().
 *
 * Converts plain text to a tsquery matching the exact phrase.
 *
 * @see https://www.postgresql.org/docs/18/textsearch-controls.html#TEXTSEARCH-PARSING-QUERIES
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT PHRASETO_TSQUERY(e.text) FROM Entity e"
 * @example Using it in DQL with config: "SELECT PHRASETO_TSQUERY('english', e.text) FROM Entity e"
 */
class PhrasetoTsquery extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'phraseto_tsquery';
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
