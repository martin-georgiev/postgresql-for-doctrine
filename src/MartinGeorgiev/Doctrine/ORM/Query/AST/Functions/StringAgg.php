<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Implementation of PostgreSql STRING_AGG().
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class StringAgg extends BaseFunction
{
    /**
     * @var bool
     */
    private $isDistinct = false;

    /**
     * @var Node
     */
    private $expression;

    /**
     * @var Node
     */
    private $delimiter;

    /**
     * @var OrderByClause|null
     */
    private $orderBy;

    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('string_agg(%s%s, %s%s)');
    }

    public function parse(Parser $parser): void
    {
        $this->customiseFunction();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(Lexer::T_DISTINCT)) {
            $parser->match(Lexer::T_DISTINCT);
            $this->isDistinct = true;
        }

        $this->expression = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->delimiter = $parser->StringPrimary();

        if ($lexer->isNextToken(Lexer::T_ORDER)) {
            $this->orderBy = $parser->OrderByClause();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [
            $this->isDistinct ? 'distinct ' : '',
            $this->expression->dispatch($sqlWalker),
            $this->delimiter->dispatch($sqlWalker),
            $this->orderBy ? $this->orderBy->dispatch($sqlWalker) : '',
        ];

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
