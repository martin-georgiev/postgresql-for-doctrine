<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\AggregateArgumentTrait;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL aggregate FILTER clause.
 *
 * Restricts the rows an aggregate reads to those matching the condition.
 *
 * @see https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-AGGREGATES
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT FILTER(COUNT(e.id), WHERE e.score > 5) FROM Entity e"
 */
class AggregateFilter extends BaseFunction implements AggregateFunction
{
    use AggregateArgumentTrait;

    private Node $aggregate;

    private Node $condition;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s FILTER (WHERE %s)');
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->aggregate = $this->parseAggregateArgument($parser);
        if ($this->aggregate instanceof self) {
            throw ParserException::forNonAggregateArgument($this->name, $this->aggregate->name);
        }

        $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
        $parser->match($shouldUseLexer ? Lexer::T_WHERE : TokenType::T_WHERE);

        // The walker appends the root entity's discriminator and SQL filters to a WhereClause; a FILTER condition must carry neither.
        $this->condition = $parser->ConditionalExpression();

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return \sprintf(
            $this->functionPrototype,
            $this->aggregate->dispatch($sqlWalker),
            $this->condition->dispatch($sqlWalker)
        );
    }
}
