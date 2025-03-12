<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL json field retrieval as integer, filtered by key (using ->> and type casting to BIGINT).
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonGetFieldAsInteger extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('CAST(%s ->> %s as BIGINT)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
