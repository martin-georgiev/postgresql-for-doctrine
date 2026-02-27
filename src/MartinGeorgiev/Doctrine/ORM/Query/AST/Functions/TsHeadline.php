<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TS_HEADLINE().
 *
 * Highlights search terms in a document based on a tsquery.
 *
 * @see https://www.postgresql.org/docs/18/textsearch-controls.html#TEXTSEARCH-HEADLINE
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TS_HEADLINE(e.text, TO_TSQUERY(:query)) FROM Entity e"
 * @example Using it in DQL with config: "SELECT TS_HEADLINE('english', e.text, TO_TSQUERY(:query)) FROM Entity e"
 * @example Using it in DQL with options: "SELECT TS_HEADLINE(e.text, TO_TSQUERY(:query), 'StartSel=<b>, StopSel=</b>') FROM Entity e"
 */
class TsHeadline extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ts_headline';
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
