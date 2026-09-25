<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Utils\DoctrineLexer;

/**
 * Parses the aggregate call a wrapper such as FILTER or OVER takes as its first argument.
 *
 * DQL's own aggregates (AVG, COUNT, MAX, MIN, SUM) are keywords the parser reads only through AggregateExpression();
 * any other function is accepted only when it implements AggregateFunction.
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
        $lookaheadValue = DoctrineLexer::getLookaheadValue($parser->getLexer());
        $isADqlAggregate = \is_string($lookaheadValue) && \in_array(\strtoupper($lookaheadValue), self::DQL_AGGREGATE_KEYWORDS, true);
        if ($isADqlAggregate) {
            return $parser->AggregateExpression();
        }

        $functionNode = $parser->FunctionDeclaration();
        if (!$functionNode instanceof AggregateFunction) {
            throw ParserException::forNonAggregateArgument($this->name, $functionNode->name);
        }

        return $functionNode;
    }
}
