<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL string concatenation (using ||).
 *
 * @see https://www.postgresql.org/docs/current/functions-string.html
 * @since 2.6.0
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class StrConcat extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('(%s || %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
