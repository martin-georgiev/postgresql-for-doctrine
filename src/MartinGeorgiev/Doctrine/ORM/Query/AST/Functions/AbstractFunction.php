<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class AbstractFunction extends FunctionNode
{
    /**
     * @var string
     */
    protected $functionPrototype;

    /**
     * @var string[]
     */
    protected $nodesMapping = [];

    /**
     * @var Node[]
     */
    protected $nodes = [];

    abstract protected function customiseFunction();

    /**
     * Sets function prototype
     *
     * @param string $functionPrototype
     */
    protected function setFunctionPrototype($functionPrototype)
    {
        $this->functionPrototype = $functionPrototype;
    }

    /**
     * Adds new node mapping
     *
     * @param string $parserMethod
     */
    protected function addNodeMapping($parserMethod)
    {
        $this->nodesMapping[] = $parserMethod;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(Parser $parser)
    {
        $this->customiseFunction();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->feedParserWithNodes($parser);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Feeds given parser with previously set nodes
     *
     * @param Parser $parser
     */
    protected function feedParserWithNodes(Parser $parser)
    {
        $nodesMappingCount = count($this->nodesMapping);
        $lastNode = $nodesMappingCount - 1;
        for ($i = 0; $i < $nodesMappingCount; $i++) {
            $parserMethod = $this->nodesMapping[$i];
            $this->nodes[$i] = $parser->$parserMethod();
            if ($i < $lastNode) {
                $parser->match(Lexer::T_COMMA);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $dispatched = [];
        foreach ($this->nodes as $node) {
            $dispatched[] = $node->dispatch($sqlWalker);
        }

        return vsprintf($this->functionPrototype, $dispatched);
    }
}
