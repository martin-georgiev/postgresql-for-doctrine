<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XML_IS_WELL_FORMED_CONTENT().
 *
 * Checks whether a text string is well-formed XML content (accepts fragments
 * and plain text nodes in addition to full documents).
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XML_IS_WELL_FORMED_CONTENT(e.xmlData) FROM Entity e"
 */
class XmlIsWellFormedContent extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xml_is_well_formed_content(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
