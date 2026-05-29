<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XMLTEXT().
 *
 * Creates an XML text node from a text string, escaping characters that are
 * not allowed directly in XML (e.g. & becomes &amp;).
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XMLTEXT(e.description) FROM Entity e"
 */
class XmlText extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xmltext(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
