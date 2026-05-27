<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XMLEXISTS().
 *
 * Tests whether an XPath expression matches any nodes in an XML value.
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
        $this->setFunctionPrototype('xmlexists(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
