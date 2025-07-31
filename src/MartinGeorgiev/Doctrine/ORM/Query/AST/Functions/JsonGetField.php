<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL json field retrieval, filtered by key (using ->).
 *
 * Supports both string keys for object property access and integer indices for array element access:
 * - JSON_GET_FIELD(json_column, 'property_name') -> json_column->'property_name'
 * - JSON_GET_FIELD(json_column, 0) -> json_column->0
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonGetField extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s -> %s)');
    }

    protected function feedParserWithNodes(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        // Parse first parameter (always StringPrimary for the JSON column)
        $this->nodes[0] = $parser->StringPrimary();
        $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);

        // Parse second parameter - try ArithmeticPrimary first, then StringPrimary
        try {
            $this->nodes[1] = $parser->ArithmeticPrimary();
        } catch (QueryException) {
            // If ArithmeticPrimary fails (e.g., when encountering a string), try StringPrimary
            $this->nodes[1] = $parser->StringPrimary();
        }
    }
}
