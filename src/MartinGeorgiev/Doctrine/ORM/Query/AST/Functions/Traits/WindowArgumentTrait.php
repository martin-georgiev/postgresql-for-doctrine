<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\AggregateExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WindowFunction;

/**
 * Parses the call OVER takes as its first argument: an aggregate, or a function implementing WindowFunction.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait WindowArgumentTrait
{
    use AggregateArgumentTrait;

    protected function parseWindowArgument(Parser $parser): AggregateExpression|FunctionNode
    {
        $call = $this->parseCallArgument($parser);
        $isWindowable = $this->isAggregate($call) || $call instanceof WindowFunction;
        if (!$isWindowable) {
            throw ParserException::forNonWindowableArgument($this->name, $call->name);
        }

        return $call;
    }
}
