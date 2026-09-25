<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\AggregateExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Utils\DoctrineLexer;

/**
 * Parses the aggregate call a clause such as FILTER takes as its first argument.
 *
 * DQL's own aggregates (AVG, COUNT, MAX, MIN, SUM) are keywords the parser reads only through AggregateExpression();
 * any other function counts as an aggregate when it implements AggregateFunction.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait AggregateArgumentTrait
{
    /**
     * @var list<string>
     */
    private const DQL_AGGREGATE_KEYWORDS = ['AVG', 'COUNT', 'MAX', 'MIN', 'SUM'];

    protected function parseAggregateArgument(Parser $parser): AggregateExpression|FunctionNode
    {
        $call = $this->parseCallArgument($parser);
        if (!$this->isAggregate($call)) {
            throw ParserException::forNonAggregateArgument($this->name, $call->name);
        }

        return $call;
    }

    private function parseCallArgument(Parser $parser): AggregateExpression|FunctionNode
    {
        $lookaheadValue = DoctrineLexer::getLookaheadValue($parser->getLexer());
        $isADqlAggregate = \is_string($lookaheadValue) && \in_array(\strtoupper($lookaheadValue), self::DQL_AGGREGATE_KEYWORDS, true);
        if ($isADqlAggregate) {
            return $parser->AggregateExpression();
        }

        return $parser->FunctionDeclaration();
    }

    /**
     * @phpstan-assert-if-false FunctionNode $call
     */
    private function isAggregate(AggregateExpression|FunctionNode $call): bool
    {
        return $call instanceof AggregateExpression || $call instanceof AggregateFunction;
    }
}
