<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseVariadicFunction extends BaseFunction
{
    /**
     * @var string
     */
    protected $commonNodeMapping = 'StringPrimary';

    public function feedParserWithNodes(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        $this->nodes[] = $parser->{$this->commonNodeMapping}();
        if (!isset($lexer->lookahead['type'])) {
            throw new \RuntimeException('The parser\'s "lookahead" property is not populated with a type');
        }

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

        return \sprintf($this->functionPrototype, \implode(', ', $dispatched));
    }
}
