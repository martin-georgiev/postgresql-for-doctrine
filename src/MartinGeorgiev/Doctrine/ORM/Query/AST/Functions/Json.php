<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL JSON().
 *
 * Parses a value as JSON. With WITH UNIQUE KEYS, it fails on an object that repeats a key.
 *
 * Note: The FORMAT JSON and ENCODING clauses are NOT supported.
 *
 * @see https://www.postgresql.org/docs/18/functions-json.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON(e.payload) FROM Entity e"
 * @example Using it in DQL with unique keys: "SELECT JSON(e.payload WITH UNIQUE KEYS) FROM Entity e"
 */
class Json extends FunctionNode
{
    public Node $expression;

    public bool $withUniqueKeys = false;

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->expression = $parser->StringPrimary();

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_WITH : TokenType::T_WITH)) {
            $parser->match($shouldUseLexer ? Lexer::T_WITH : TokenType::T_WITH);
            $this->matchKeyword($parser, 'UNIQUE');
            if ($this->isNextKeyword($parser, 'KEYS')) {
                $this->matchKeyword($parser, 'KEYS');
            }

            $this->withUniqueKeys = true;
        }

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * DQL does not know UNIQUE or KEYS, so the lexer hands them over as identifiers.
     */
    private function isNextKeyword(Parser $parser, string $keyword): bool
    {
        $lexer = $parser->getLexer();
        $identifierType = DoctrineOrm::isPre219() ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER;
        $value = DoctrineLexer::getLookaheadValue($lexer);

        return DoctrineLexer::getLookaheadType($lexer) === $identifierType
            && \is_string($value)
            && \strtoupper($value) === $keyword;
    }

    private function matchKeyword(Parser $parser, string $keyword): void
    {
        if (!$this->isNextKeyword($parser, $keyword)) {
            $parser->syntaxError($keyword);
        }

        $parser->match(DoctrineOrm::isPre219() ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $expression = $this->expression->dispatch($sqlWalker);

        return $this->withUniqueKeys
            ? \sprintf('json(%s WITH UNIQUE KEYS)', $expression)
            : \sprintf('json(%s)', $expression);
    }
}
