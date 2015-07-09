<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

abstract class AbstractFunction extends FunctionNode
{
    private $functionPrototype;
    private $literalsMapping = [];
    private $literals = [];

    protected function customiseFunction();
    
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
     * Adds new literal mapping
     * 
     * @param string $parserMethod
     */
    protected function addLiteralMapping($parserMethod) 
    {
        $this->literalsMapping[] = $parserMethod;
    }
    
    /**
     * {@inheritDoc}
     */
    public function parse(Parser $parser)
    {
        $this->customiseFunction();
        
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->feedParserWithLiterals($parser);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
    
    /**
     * Feeds given parser with previously set literals
     * 
     * @param Parser $parser
     */
    protected function feedParserWithLiterals(Parser $parser)
    {
        $literalsMappingCount = count($this->literalsMapping);
        $lastLitteral = $literalsMappingCount - 1;
        for ($i = 0; $i < $literalsMappingCount; $i++) {
            $parserMethod = $this->literalsMapping[$i];
            $this->literals[$i] = $parser->$parserMethod();
            if ($i < $lastLitteral) {
                $parser->match(Lexer::T_COMMA);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $dispateched = [];
        foreach ($this->literals as $literal) {
            $dispateched[] = $literal->dispatch($sqlWalker);
        }
        return vsprintf($this->functionPrototype, $dispateched);
    }
    
}
