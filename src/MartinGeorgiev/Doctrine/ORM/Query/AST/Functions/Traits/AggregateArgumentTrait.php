<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

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
    protected function parseAggregateArgument(Parser $parser): Node
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $dqlAggregateTokens = $shouldUseLexer
            ? [Lexer::T_AVG, Lexer::T_COUNT, Lexer::T_MAX, Lexer::T_MIN, Lexer::T_SUM]
            : [TokenType::T_AVG, TokenType::T_COUNT, TokenType::T_MAX, TokenType::T_MIN, TokenType::T_SUM];

        if (\in_array(DoctrineLexer::getLookaheadType($parser->getLexer()), $dqlAggregateTokens, true)) {
            return $parser->AggregateExpression();
        }

        $functionNode = $parser->FunctionDeclaration();
        if (!$functionNode instanceof AggregateFunction) {
            throw ParserException::forNonAggregateArgument($this->name, $functionNode->name);
        }

        return $functionNode;
    }
}
