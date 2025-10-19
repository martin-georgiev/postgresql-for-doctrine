<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL's string concatenation operator (using `||`).
 *
 * @see https://www.postgresql.org/docs/15/functions-string.html
 * @since 2.6
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
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
