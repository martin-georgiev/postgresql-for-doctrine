<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL NOT SIMILAR TO.
 *
 * @see https://www.postgresql.org/docs/9.6/functions-matching.html
 *
 * @author Igor Lazarev <strider2038@yandex.ru>
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
