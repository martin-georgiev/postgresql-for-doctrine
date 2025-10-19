<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL WEBSEARCH_TO_TSQUERY().
 *
 * @see https://www.postgresql.org/docs/17/textsearch-controls.html
 * @since 3.5
 *
 * @author Jan Klan <jan@klan.com.au>
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
