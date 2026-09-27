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
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
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

        return ' '.$this->walkOrderByClauseLeavingTheOuterQueryOrderIntact($sqlWalker, $this->orderByClause);
    }

    /**
     * Doctrine's walkOrderByClause() appends the #[OrderBy] columns of a fetch-joined collection, and walkOrderByItem()
     * records each column it renders so the outer query skips it. Both belong to the outer query, not to this ORDER BY.
     */
    private function walkOrderByClauseLeavingTheOuterQueryOrderIntact(SqlWalker $sqlWalker, OrderByClause $orderByClause): string
    {
        $orderByItems = \array_map(
            static function (OrderByItem $orderByItem) use ($sqlWalker): string {
                $expression = $orderByItem->expression;
                $isASelectListAlias = !$expression instanceof Node;
                if ($isASelectListAlias) {
                    throw ParserException::forSelectListAliasInOrderBy();
                }

                $sql = $expression->dispatch($sqlWalker);

                return ($expression instanceof Subselect ? '('.$sql.')' : $sql).' '.\strtoupper($orderByItem->type);
            },
            $orderByClause->orderByItems
        );

        return 'ORDER BY '.\implode(', ', $orderByItems);
    }
}
