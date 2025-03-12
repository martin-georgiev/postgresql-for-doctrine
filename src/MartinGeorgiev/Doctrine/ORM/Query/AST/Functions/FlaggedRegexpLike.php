<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_LIKE() with flags.
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @see https://www.postgresql.org/docs/15/functions-matching.html#POSIX-EMBEDDED-OPTIONS-TABLE
 * @since 2.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class FlaggedRegexpLike extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('regexp_like(%s, %s, 1, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
