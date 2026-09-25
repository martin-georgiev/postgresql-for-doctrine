<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Utils\DoctrineLexer;

/**
 * Parses the call a wrapper such as FILTER or OVER takes as its first argument.
 *
 * DQL's own aggregates (AVG, COUNT, MAX, MIN, SUM) are keywords the parser reads only through AggregateExpression();
 * FILTER accepts any other function only when it implements AggregateFunction.
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

    protected function parseAggregateArgument(Parser $parser): Node
    {
        $argument = $this->parseDqlAggregateOrFunction($parser);
        if ($argument instanceof FunctionNode && !$argument instanceof AggregateFunction) {
            throw ParserException::forNonAggregateArgument($this->name, $argument->name);
        }

        return $argument;
    }

    protected function parseDqlAggregateOrFunction(Parser $parser): Node
    {
        $lookaheadValue = DoctrineLexer::getLookaheadValue($parser->getLexer());
        $isADqlAggregate = \is_string($lookaheadValue) && \in_array(\strtoupper($lookaheadValue), self::DQL_AGGREGATE_KEYWORDS, true);
        if ($isADqlAggregate) {
            return $parser->AggregateExpression();
        }

        return $parser->FunctionDeclaration();
    }
}
