<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseVariadicFunction extends BaseFunction
{
    protected string $commonNodeMapping = 'StringPrimary';

    public function feedParserWithNodes(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        $this->nodes[] = $parser->{$this->commonNodeMapping}();
        if ($lexer->lookahead?->type === null) {
            throw ParserException::missingLookaheadType();
        }

        $aheadType = $lexer->lookahead->type;
        $shouldUseLexer = DoctrineOrm::isPre219();

        while (($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS) !== $aheadType) {
            if (($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA) === $aheadType) {
                $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
                $this->nodes[] = $parser->{$this->commonNodeMapping}();
            }
            $aheadType = $lexer->lookahead->type;
        }
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [];
        foreach ($this->nodes as $node) {
            $dispatched[] = $node instanceof Node ? $node->dispatch($sqlWalker) : 'null';
        }

        return \sprintf($this->functionPrototype, \implode(', ', $dispatched));
    }
}
