<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseVariadicFunction extends BaseFunction
{
    /**
     * @return array<int|string, string>
     */
    abstract protected function getNodeMappingPattern(): array;

    /**
     * @throws ParserException
     */
    protected function feedParserWithNodes(Parser $parser): void
    {
        foreach ($this->getNodeMappingPattern() as $nodeMappingPattern) {
            try {
                $this->feedParserWithNodesForNodeMappingPattern($parser, $nodeMappingPattern);

                break;
            } catch (ParserException) {
                // swallow and continue with next pattern
            }
        }

        $this->validateArguments(...$this->nodes); // @phpstan-ignore-line
    }

    private function feedParserWithNodesForNodeMappingPattern(Parser $parser, string $nodeMappingPattern): void
    {
        $nodeMapping = \explode(',', $nodeMappingPattern);
        $lexer = $parser->getLexer();

        try {
            // @phpstan-ignore-next-line
            $this->nodes[] = $parser->{$nodeMapping[0]}();
            $lookaheadType = DoctrineLexer::getLookaheadType($lexer);
            if ($lookaheadType === null) {
                throw ParserException::missingLookaheadType();
            }
        } catch (\Throwable $throwable) {
            throw ParserException::withThrowable($throwable);
        }

        $shouldUseLexer = DoctrineOrm::isPre219();
        $isNodeMappingASimplePattern = \count($nodeMapping) === 1;
        $nodeIndex = 1;
        while (($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS) !== $lookaheadType) {
            if (($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA) === $lookaheadType) {
                $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
                // @phpstan-ignore-next-line
                $this->nodes[] = $parser->{$nodeMapping[$isNodeMappingASimplePattern ? 0 : $nodeIndex]}();
                $nodeIndex++;
            }

            $lookaheadType = DoctrineLexer::getLookaheadType($lexer);
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

    /**
     * Validates the arguments passed to the function.
     *
     * @throws InvalidArgumentForVariadicFunctionException
     */
    abstract protected function validateArguments(Node ...$arguments): void;
}
