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

        $nodeForJsonDocumentName = $parser->StringPrimary();
        $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);

        // Second parameter can be either an index or a property name
        try {
            $nodeForJsonIndexOrPropertyName = $parser->ArithmeticPrimary();
        } catch (QueryException) {
            // If ArithmeticPrimary fails (e.g., when encountering a property name rather than an index), try StringPrimary
            $nodeForJsonIndexOrPropertyName = $parser->StringPrimary();
        }

        /* @phpstan-ignore-next-line assign.propertyType */
        $this->nodes = [$nodeForJsonDocumentName, $nodeForJsonIndexOrPropertyName];
    }
}
