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
        if (!$this->orderByClause instanceof OrderByClause) {
            return '';
        }

        // Walking the items instead of the clause keeps Doctrine from appending the #[OrderBy] columns of a
        // fetch-joined collection, which belong to the outer query and not to the aggregate.
        $orderByItems = \array_map($sqlWalker->walkOrderByItem(...), $this->orderByClause->orderByItems);

        return ' ORDER BY '.\implode(', ', $orderByItems);
    }
}
