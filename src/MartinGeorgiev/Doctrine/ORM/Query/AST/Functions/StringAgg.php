<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL STRING_AGG().
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class StringAgg extends BaseOrderableFunction
{
    private bool $isDistinct = false;

    private Node $delimiter;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('string_agg(%s%s, %s%s)');
    }

    protected function parseFunction(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_DISTINCT : TokenType::T_DISTINCT)) {
            $parser->match($shouldUseLexer ? Lexer::T_DISTINCT : TokenType::T_DISTINCT);
            $this->isDistinct = true;
        }

        $this->expression = $parser->StringPrimary();

        $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);

        $this->delimiter = $parser->StringPrimary();
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [
            $this->isDistinct ? 'DISTINCT ' : '',
            $this->expression->dispatch($sqlWalker),
            $this->delimiter->dispatch($sqlWalker),
            $this->getOptionalOrderByClause($sqlWalker),
        ];

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
