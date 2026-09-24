<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\AggregateArgumentTrait;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\WindowSpecificationTrait;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL OVER clause for aggregate functions.
 *
 * Computes the aggregate over the window of each row instead of collapsing the rows into one.
 *
 * @see https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-WINDOW-FUNCTIONS
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(SUM(e.amount), PARTITION BY e.customer ORDER BY e.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) FROM Entity e"
 */
class AggregateOver extends BaseFunction
{
    use AggregateArgumentTrait;
    use WindowSpecificationTrait;

    private Node $aggregate;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s OVER (%s)');
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->aggregate = $this->parseAggregateArgument($parser);

        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA)) {
            $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);

            if (!$this->isWindowSpecificationNext($lexer)) {
                $parser->syntaxError('PARTITION BY, ORDER BY or a frame clause');
            }

            $this->parseWindowSpecification($parser);
        }

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return \sprintf(
            $this->functionPrototype,
            $this->aggregate->dispatch($sqlWalker),
            $this->getWindowSpecificationSql($sqlWalker)
        );
    }
}
