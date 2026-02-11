<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL WEBSEARCH_TO_TSQUERY().
 *
 * Converts a web search string to a text search query.
 *
 * @see https://www.postgresql.org/docs/17/textsearch-controls.html
 * @since 3.5
 *
 * @author Jan Klan <jan@klan.com.au>
 *
 * @example Using it in DQL: "SELECT WEBSEARCH_TO_TSQUERY(e.search_text) FROM Entity e"
 */
class WebsearchToTsquery extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'websearch_to_tsquery';
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
