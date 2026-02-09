<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL NOT SIMILAR TO operator.
 *
 * Performs SQL pattern non-matching.
 *
 * @see https://www.postgresql.org/docs/9.6/functions-matching.html
 * @since 1.3
 *
 * @author Igor Lazarev <strider2038@yandex.ru>
 *
 * @example Using it in DQL with boolean comparison: "WHERE NOT_SIMILAR_TO(e.text, 'pattern') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class NotSimilarTo extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s not similar to %s');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
