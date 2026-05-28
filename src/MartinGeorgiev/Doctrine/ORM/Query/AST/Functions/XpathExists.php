<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XPATH_EXISTS().
 *
 * Returns true if the XPath expression matches any node in the XML value.
 * The optional third argument accepts a namespace mapping array.
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE XPATH_EXISTS('//item', e.xmlData) = TRUE"
 */
class XpathExists extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'xpath_exists';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
