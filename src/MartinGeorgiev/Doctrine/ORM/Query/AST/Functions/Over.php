<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\AggregateExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\WindowArgumentTrait;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\WindowSpecificationTrait;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL OVER clause.
 *
 * Runs an aggregate or a window function over the window of each row instead of collapsing the rows into one.
 *
 * @see https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-WINDOW-FUNCTIONS
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(SUM(e.amount), PARTITION BY e.customer ORDER BY e.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) FROM Entity e"
 * @example Using it in DQL with null treatment: "SELECT OVER(LAG(e.price) IGNORE NULLS, ORDER BY e.day) FROM Entity e"
 */
class Over extends BaseFunction
{
    use WindowArgumentTrait;
    use WindowSpecificationTrait;

    private AggregateExpression|FunctionNode $windowedCall;

    private ?string $nullTreatment = null;

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

        $this->windowedCall = $this->parseWindowArgument($parser);
        $this->parseNullTreatment($parser);

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
        $windowedCallSql = $this->windowedCall->dispatch($sqlWalker);
        if ($this->nullTreatment !== null) {
            $windowedCallSql .= \sprintf(' %s NULLS', $this->nullTreatment);
        }

        return \sprintf(
            $this->functionPrototype,
            $windowedCallSql,
            $this->getWindowSpecificationSql($sqlWalker)
        );
    }

    private function parseNullTreatment(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        foreach (['IGNORE', 'RESPECT'] as $nullTreatment) {
            if (!$this->isKeywordIdentifier($lexer->lookahead, $nullTreatment)) {
                continue;
            }

            if (!$this->windowedCall instanceof NullTreatableWindowFunction) {
                $argumentName = $this->windowedCall instanceof AggregateExpression ? $this->windowedCall->functionName : $this->windowedCall->name;

                throw ParserException::forNullTreatmentOfUnsupportedArgument($this->name, $argumentName);
            }

            $this->matchKeywordIdentifier($parser, $nullTreatment);
            $this->matchKeywordIdentifier($parser, 'NULLS');
            $this->nullTreatment = $nullTreatment;

            return;
        }
    }
}
