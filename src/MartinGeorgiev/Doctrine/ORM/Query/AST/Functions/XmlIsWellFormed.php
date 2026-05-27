<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XML_IS_WELL_FORMED().
 *
 * Checks whether a text string is well-formed XML.
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XML_IS_WELL_FORMED(e.text1) FROM Entity e"
 */
class XmlIsWellFormed extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xml_is_well_formed(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
