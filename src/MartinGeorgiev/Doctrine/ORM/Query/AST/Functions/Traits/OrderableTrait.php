<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\AST\OrderByItem;
use Doctrine\ORM\Query\AST\Subselect;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineLexer;
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

        return ' ORDER BY '.$this->walkOrderByItemsLeavingTheOuterQueryOrderIntact($sqlWalker, $this->orderByClause);
    }

    /**
     * Doctrine's walkOrderByClause() appends the #[OrderBy] columns of a fetch-joined collection, and walkOrderByItem()
     * records each column it renders so the outer query skips it. Both belong to the outer query, not to this ORDER BY.
     */
    private function walkOrderByItemsLeavingTheOuterQueryOrderIntact(SqlWalker $sqlWalker, OrderByClause $orderByClause): string
    {
        $orderByItems = \array_map(
            static function (OrderByItem $orderByItem) use ($sqlWalker): string {
                $expression = $orderByItem->expression;
                $sortDirection = \strtoupper($orderByItem->type);
                if ($expression instanceof Node) {
                    $sql = $expression->dispatch($sqlWalker);

                    return ($expression instanceof Subselect ? '('.$sql.')' : $sql).' '.$sortDirection;
                }

                \assert(\is_string($expression));
                $resultVariable = DoctrineLexer::getTokenField($sqlWalker->getQueryComponent($expression)['token'], 'value');
                \assert(\is_string($resultVariable));

                return $sqlWalker->walkResultVariable($resultVariable).' '.$sortDirection;
            },
            $orderByClause->orderByItems
        );

        return \implode(', ', $orderByItems);
    }
}
