<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL XMLEXISTS().
 *
 * Tests whether an XPath expression matches any nodes in an XML value.
 *
 * PostgreSQL requires PASSING BY VALUE syntax; DQL uses a comma-separated call:
 * XMLEXISTS(xpath, xml) → xmlexists(xpath PASSING BY VALUE xml)
 *
 * @see https://www.postgresql.org/docs/18/functions-xml.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE XMLEXISTS('//item', e.xmlData) = TRUE"
 */
class XmlExists extends BaseFunction
{
    private Node $xpath;

    private Node $xml;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xmlexists(%s PASSING BY VALUE %s)');
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);
        $this->xpath = $parser->StringPrimary();
        $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
        $this->xml = $parser->StringPrimary();
        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return \sprintf(
            $this->functionPrototype,
            $this->xpath->dispatch($sqlWalker),
            $this->xml->dispatch($sqlWalker)
        );
    }
}
