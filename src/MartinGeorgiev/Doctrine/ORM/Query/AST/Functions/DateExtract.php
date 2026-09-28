<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidFieldNameException;

/**
 * Implementation of PostgreSQL EXTRACT().
 *
 * Extracts a field from a date or timestamp.
 *
 * @see https://www.postgresql.org/docs/14/functions-datetime.html
 * @since 2.1
 *
 * @author Keith Brink <keith.brink@gmail.com>
 *
 * @example Using it in DQL: "SELECT DATE_EXTRACT('year', e.created_at) FROM Entity e"
 */
class DateExtract extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('EXTRACT(%s FROM %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }

    /**
     * EXTRACT(field FROM source) takes the field as a keyword or a string literal. A parameter, a column or a function
     * call there is a syntax error in PostgreSQL.
     */
    protected function feedParserWithNodes(Parser $parser): void
    {
        parent::feedParserWithNodes($parser);

        $field = $this->nodes[0];
        $isAStringLiteral = $field instanceof Literal && $field->type === Literal::STRING;
        if (!$isAStringLiteral) {
            throw InvalidFieldNameException::forNonStringValue('DATE_EXTRACT');
        }
    }
}
