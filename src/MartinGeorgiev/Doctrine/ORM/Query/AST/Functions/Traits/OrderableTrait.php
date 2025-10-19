<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

trait OrderableTrait
{
    protected Node $expression;

    protected ?OrderByClause $orderByClause = null;

    protected function parseOrderByClause(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_ORDER : TokenType::T_ORDER)) {
            $this->orderByClause = $parser->OrderByClause();
        }
    }

    protected function getOptionalOrderByClause(SqlWalker $sqlWalker): string
    {
        return $this->orderByClause instanceof OrderByClause ? $this->orderByClause->dispatch($sqlWalker) : '';
    }
}
