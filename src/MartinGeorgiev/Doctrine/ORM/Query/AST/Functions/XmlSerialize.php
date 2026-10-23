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
 * Implementation of PostgreSQL XMLSERIALIZE().
 *
 * Serializes an XML value as a document or as content into a character string, optionally indented.
 *
 * Note: The target type is TEXT or VARCHAR.
 *
 * @see https://www.postgresql.org/docs/18/datatype-xml.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XMLSERIALIZE(DOCUMENT e.xmlData AS TEXT) FROM Entity e"
 * @example Using it in DQL with indent: "SELECT XMLSERIALIZE(DOCUMENT e.xmlData AS TEXT INDENT) FROM Entity e"
 */
class XmlSerialize extends FunctionNode
{
    /**
     * @var array
     */
    private const OPTIONS = ['DOCUMENT', 'CONTENT'];

    /**
     * @var array
     */
    private const TARGET_TYPES = ['TEXT', 'VARCHAR'];

    public string $option;

    public Node $expression;

    public string $targetType;

    public ?string $indent = null;

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->option = $this->matchOneOfKeywords($parser, self::OPTIONS);
        $this->expression = $parser->StringPrimary();
        $parser->match($shouldUseLexer ? Lexer::T_AS : TokenType::T_AS);
        $this->targetType = $this->matchOneOfKeywords($parser, self::TARGET_TYPES);

        if ($this->isNextKeyword($parser, 'NO')) {
            $this->matchOneOfKeywords($parser, ['NO']);
            $this->matchOneOfKeywords($parser, ['INDENT']);
            $this->indent = 'NO INDENT';
        } elseif ($this->isNextKeyword($parser, 'INDENT')) {
            $this->matchOneOfKeywords($parser, ['INDENT']);
            $this->indent = 'INDENT';
        }

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * DQL does not know any of the XMLSERIALIZE keywords, so the lexer hands them over as identifiers.
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

    /**
     * @param list<string> $keywords
     */
    private function matchOneOfKeywords(Parser $parser, array $keywords): string
    {
        foreach ($keywords as $keyword) {
            if ($this->isNextKeyword($parser, $keyword)) {
                $parser->match(DoctrineOrm::isPre219() ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);

                return $keyword;
            }
        }

        $parser->syntaxError(\implode(' or ', $keywords));
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $sql = \sprintf('xmlserialize(%s %s AS %s', $this->option, $this->expression->dispatch($sqlWalker), $this->targetType);
        if ($this->indent !== null) {
            $sql .= ' '.$this->indent;
        }

        return $sql.')';
    }
}
