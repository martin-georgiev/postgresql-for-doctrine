<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL XMLCONCAT().
 *
 * Concatenates multiple XML values into a single XML value.
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XMLCONCAT(e.text1, e.text2) FROM Entity e"
 */
class XmlConcat extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'xmlconcat';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return PHP_INT_MAX; // No upper limit
    }
}
