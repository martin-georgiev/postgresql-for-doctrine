<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseFunction extends FunctionNode
{
    protected string $functionPrototype;

    /**
     * @var list<string>
     */
    protected array $nodesMapping = [];

    /**
     * @var list<Node|null>
     */
    protected array $nodes = [];

    /**
     * This method is meant for internal use only, and it is not suggested that the forks of the library depend on it.
     * It will be made abstract from version 3.0.
     *
     * @internal
     *
     * @see customiseFunction()
     */
    /* abstract */
    protected function customizeFunction(): void
    {
        // Void
    }

    /**
     * @deprecated
     */
    protected function customiseFunction(): void
    {
        \trigger_error('The internal-use method of `customiseFunction()` is deprecated and is now renamed to `customizeFunction()`. `customiseFunction()` will be removed from version 3.0 onwards.', E_USER_DEPRECATED);
        $this->customizeFunction();
    }

    protected function setFunctionPrototype(string $functionPrototype): void
    {
        $this->functionPrototype = $functionPrototype;
    }

    protected function addNodeMapping(string $parserMethod): void
    {
        $this->nodesMapping[] = $parserMethod;
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);
        $this->feedParserWithNodes($parser);
        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * Feeds given parser with previously set nodes.
     */
    protected function feedParserWithNodes(Parser $parser): void
    {
        $nodesMappingCount = \count($this->nodesMapping);
        $lastNode = $nodesMappingCount - 1;
        for ($i = 0; $i < $nodesMappingCount; $i++) {
            $parserMethod = $this->nodesMapping[$i];
            // @phpstan-ignore-next-line
            $this->nodes[$i] = $parser->{$parserMethod}();
            if ($i < $lastNode) {
                $parser->match(\class_exists(TokenType::class) ? TokenType::T_COMMA : Lexer::T_COMMA);
            }
        }
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [];
        foreach ($this->nodes as $node) {
            $dispatched[] = $node instanceof Node ? $node->dispatch($sqlWalker) : 'null';
        }

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
