<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL MD5().
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT MD5(e.email) FROM Entity e"
 */
class Md5 extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('md5(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
