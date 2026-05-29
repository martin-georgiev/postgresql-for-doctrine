<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XMLEXISTS().
 *
 * Tests whether an XPath expression matches any nodes in an XML value.
 *
 * The second argument must be a well-formed XML document, not a content fragment.
 * Passing a fragment returns NULL rather than false.
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE XMLEXISTS('//item', e.xmlData) = TRUE"
 */
class XmlExists extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xmlexists(%s PASSING BY VALUE %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
