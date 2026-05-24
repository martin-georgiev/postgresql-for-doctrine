<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XPATH_EXISTS().
 *
 * Returns true if the XPath expression matches any node in the XML value.
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XPATH_EXISTS(e.text1, e.text2) FROM Entity e"
 */
class XpathExists extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xpath_exists(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
