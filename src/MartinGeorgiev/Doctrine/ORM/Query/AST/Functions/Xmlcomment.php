<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XMLCOMMENT().
 *
 * Creates an XML comment.
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XMLCOMMENT(e.text1) FROM Entity e"
 */
class Xmlcomment extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xmlcomment(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
