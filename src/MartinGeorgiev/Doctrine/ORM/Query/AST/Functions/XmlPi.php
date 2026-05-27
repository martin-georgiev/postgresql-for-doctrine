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
 * Implementation of PostgreSQL XMLPI().
 *
 * Creates an XML processing instruction.
 *
 * PostgreSQL requires NAME keyword syntax: XMLPI(NAME target [, content])
 * The target must be a string literal in DQL — it becomes a SQL NAME identifier.
 * The optional content can be a literal or entity property.
 *
 * DQL: XMLPI('target')           → SQL: xmlpi(NAME target)
 * DQL: XMLPI('target', content)  → SQL: xmlpi(NAME target, content)
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XMLPI('php') FROM Entity e"
 * @example Using it in DQL with content: "SELECT XMLPI('php', e.scriptContent) FROM Entity e"
 */
class XmlPi extends BaseFunction
{
    private string $target = '';

    private ?Node $content = null;

    protected function customizeFunction(): void
    {
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $parser->match($shouldUseLexer ? Lexer::T_STRING : TokenType::T_STRING);
        $target = DoctrineLexer::getTokenValue($lexer);
        $this->target = \is_string($target) ? $target : '';

        $commaType = $shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA;
        if (DoctrineLexer::getLookaheadType($lexer) === $commaType) {
            $parser->match($commaType);
            $this->content = $parser->StringPrimary();
        }

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        if ($this->content !== null) {
            return \sprintf('xmlpi(NAME %s, %s)', $this->target, $this->content->dispatch($sqlWalker));
        }

        return \sprintf('xmlpi(NAME %s)', $this->target);
    }
}
