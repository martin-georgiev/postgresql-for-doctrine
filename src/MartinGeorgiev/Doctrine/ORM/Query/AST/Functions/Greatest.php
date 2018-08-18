<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Implementation of PostgreSql GREATEST()
 * @see https://www.postgresql.org/docs/9.4/static/functions-conditional.html#FUNCTIONS-GREATEST-LEAST
 *
 * @since 0.7
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Greatest extends AbstractFunction
{
    /**
     * @var string
     */
    protected $commonLiteralMapping = 'ArithmeticPrimary';

    /**
     * {@inheritDoc}
     */
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('greatest(%s)');
    }

    /**
     * {@inheritDoc}
     */
    public function feedParserWithLiterals(Parser $parser)
    {
        $lexer = $parser->getLexer();

        $this->literals[] = $parser->{$this->commonLiteralMapping}();

        $aheadType = $lexer->lookahead['type'];
        while (Lexer::T_CLOSE_PARENTHESIS !== $aheadType) {
            if (Lexer::T_COMMA === $aheadType) {
                $parser->match(Lexer::T_COMMA);
                $this->literals[] = $parser->{$this->commonLiteralMapping}();
            }
            $aheadType = $lexer->lookahead['type'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $dispatched = [];
        foreach ($this->literals as $literal) {
            $dispatched[] = $literal->dispatch($sqlWalker);
        }

        return sprintf($this->functionPrototype, implode(',', $dispatched));
    }
}
