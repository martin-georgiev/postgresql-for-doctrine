<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Parses an aggregate call passed as an argument, so a wrapper can append a clause after its closing parenthesis.
 *
 * DQL's own aggregates (AVG, COUNT, MAX, MIN, SUM) are keywords the parser reads only through AggregateExpression();
 * everything else, including this library's aggregates and other wrappers, is a function declaration.
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

        return $parser->FunctionDeclaration();
    }
}
