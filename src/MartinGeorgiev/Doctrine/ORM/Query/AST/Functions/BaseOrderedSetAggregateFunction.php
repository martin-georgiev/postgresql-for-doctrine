<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\OrderableTrait;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Parses an ordered-set aggregate as FUNCTION(direct arguments WITHIN GROUP ORDER BY ...).
 *
 * DQL cannot express a clause after the closing parenthesis, so the WITHIN GROUP part moves inside it.
 * WITHIN is not a DQL keyword and is matched by its value; GROUP, ORDER and BY are.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseOrderedSetAggregateFunction extends BaseFunction
{
    use OrderableTrait;

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->feedParserWithNodes($parser);

        $lookaheadValue = DoctrineLexer::getLookaheadValue($parser->getLexer());
        $isWithinKeyword = \is_string($lookaheadValue) && \strtoupper($lookaheadValue) === 'WITHIN';
        if (!$isWithinKeyword) {
            $parser->syntaxError('WITHIN GROUP');
        }

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_GROUP : TokenType::T_GROUP);

        $this->orderByClause = $parser->OrderByClause();

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [];
        foreach ($this->nodes as $node) {
            $dispatched[] = $node instanceof Node ? $node->dispatch($sqlWalker) : 'null';
        }

        // The walker prefixes the clause with a space, sized for following an argument rather than an opening parenthesis.
        $dispatched[] = \ltrim($this->getOptionalOrderByClause($sqlWalker));

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
