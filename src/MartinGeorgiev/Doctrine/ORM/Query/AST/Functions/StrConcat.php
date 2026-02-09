<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL || operator.
 *
 * Concatenates strings.
 *
 * @see https://www.postgresql.org/docs/15/functions-string.html
 * @since 2.6
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 *
 * @example Using it in DQL: "SELECT STR_CONCAT(e.firstName, e.lastName) FROM Entity e"
 */
class StrConcat extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s || %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
