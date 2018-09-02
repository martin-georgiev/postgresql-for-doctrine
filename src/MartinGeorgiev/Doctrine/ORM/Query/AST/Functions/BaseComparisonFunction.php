<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @since 1.0
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseComparisonFunction extends BaseFunction
{
    /**
     * @var string
     */
    protected $commonNodeMapping = 'ArithmeticPrimary';

    public function feedParserWithNodes(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        $this->nodes[] = $parser->{$this->commonNodeMapping}();

        $aheadType = $lexer->lookahead['type'];
        while (Lexer::T_CLOSE_PARENTHESIS !== $aheadType) {
            if (Lexer::T_COMMA === $aheadType) {
                $parser->match(Lexer::T_COMMA);
                $this->nodes[] = $parser->{$this->commonNodeMapping}();
            }
            $aheadType = $lexer->lookahead['type'];
        }
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [];
        foreach ($this->nodes as $node) {
            $dispatched[] = $node->dispatch($sqlWalker);
        }

        return sprintf($this->functionPrototype, implode(',', $dispatched));
    }
}
