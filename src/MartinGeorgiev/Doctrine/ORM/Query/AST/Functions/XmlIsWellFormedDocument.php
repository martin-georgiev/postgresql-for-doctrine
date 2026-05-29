<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XML_IS_WELL_FORMED_DOCUMENT().
 *
 * Checks whether a text string is a well-formed XML document (requires a
 * single root element — plain text or fragments return false).
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XML_IS_WELL_FORMED_DOCUMENT(e.xmlData) FROM Entity e"
 */
class XmlIsWellFormedDocument extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xml_is_well_formed_document(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
