<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\OrderableTrait;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL XMLAGG().
 *
 * Aggregates XML values.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT XML_AGG(e.xml_data) FROM Entity e"
 */
class XmlAgg extends BaseFunction
{
    use OrderableTrait;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('xmlagg(%s%s)');
        $this->addNodeMapping('StringPrimary');
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->expression = $parser->StringPrimary();
        $this->parseOrderByClause($parser);

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [
            $this->expression->dispatch($sqlWalker),
            $this->getOptionalOrderByClause($sqlWalker),
        ];

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
