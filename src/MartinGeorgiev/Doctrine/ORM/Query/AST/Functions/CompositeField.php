<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL composite type field access.
 *
 * Generates the (column).field syntax required for accessing fields of composite types.
 *
 * @see https://www.postgresql.org/docs/17/rowtypes.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT COMPOSITE_FIELD(e.compositeColumn, 'fieldName') FROM Entity e"
 * @example Using it in DQL with WHERE: "WHERE COMPOSITE_FIELD(e.item, 'price') > 9.99"
 */
class CompositeField extends BaseFunction
{
    private Node $compositeColumn;

    private string $fieldName;

    protected function customizeFunction(): void {}

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->compositeColumn = $parser->StringPrimary();

        $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);

        $parser->match($shouldUseLexer ? Lexer::T_STRING : TokenType::T_STRING);

        $fieldName = DoctrineLexer::getTokenValue($parser->getLexer());
        if (!\is_string($fieldName)) {
            return;
        }

        $this->fieldName = $fieldName;

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $columnSql = $this->compositeColumn->dispatch($sqlWalker);

        return \sprintf('(%s).%s', $columnSql, $this->fieldName);
    }
}
