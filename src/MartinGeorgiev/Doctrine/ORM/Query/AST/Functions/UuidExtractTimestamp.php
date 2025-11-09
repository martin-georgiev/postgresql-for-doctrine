<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL UUID_EXTRACT_TIMESTAMP().
 *
 * @see https://www.postgresql.org/docs/17/functions-uuid.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class UuidExtractTimestamp extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('uuid_extract_timestamp(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
