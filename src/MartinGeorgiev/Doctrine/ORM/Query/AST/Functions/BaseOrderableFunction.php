<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

abstract class BaseOrderableFunction extends BaseFunction
{
    protected Node $expression;

    protected ?OrderByClause $orderByClause = null;

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->parseFunction($parser);

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_ORDER : TokenType::T_ORDER)) {
            $this->orderByClause = $parser->OrderByClause();
        }

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    abstract protected function parseFunction(Parser $parser): void;

    protected function getOptionalOrderByClause(SqlWalker $sqlWalker): string
    {
        return $this->orderByClause instanceof OrderByClause ? $this->orderByClause->dispatch($sqlWalker) : '';
    }
}
