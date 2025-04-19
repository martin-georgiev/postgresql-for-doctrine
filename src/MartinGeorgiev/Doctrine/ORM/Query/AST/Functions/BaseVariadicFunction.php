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
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype(\sprintf('%s(%%s)', $this->getFunctionName()));
    }

    abstract protected function getFunctionName(): string;

    /**
     * @return array<string>
     */
    abstract protected function getNodeMappingPattern(): array;

    abstract protected function getMinArgumentCount(): int;

    abstract protected function getMaxArgumentCount(): int;

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
    }

    /**
     * @throws InvalidArgumentForVariadicFunctionException
     * @throws ParserException
     */
    private function feedParserWithNodesForNodeMappingPattern(Parser $parser, string $nodeMappingPattern): void
    {
        $nodeMapping = \explode(',', $nodeMappingPattern);
        $lexer = $parser->getLexer();

        try {
            $lookaheadType = DoctrineLexer::getLookaheadType($lexer);
            if ($lookaheadType === null) {
                throw InvalidArgumentForVariadicFunctionException::atLeast($this->getFunctionName(), $this->getMinArgumentCount());
            }

            $this->nodes[] = $parser->{$nodeMapping[0]}(); // @phpstan-ignore-line
        } catch (\Throwable $throwable) {
            throw ParserException::withThrowable($throwable);
        }

        $shouldUseLexer = DoctrineOrm::isPre219();
        $isNodeMappingASimplePattern = \count($nodeMapping) === 1;
        $nodeIndex = 1;
        while (($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS) !== $lookaheadType) {
            if (($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA) === $lookaheadType) {
                $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);

                // Check if we're about to exceed the maximum number of arguments
                // nodeIndex starts at 1 and counts up for each argument after the first
                // So when nodeIndex=1, we're about to add the 2nd argument (total: 2)
                // When nodeIndex=2, we're about to add the 3rd argument (total: 3)
                $foundMoreNodesThanMappingExpected = ($nodeIndex + 1) > $this->getMaxArgumentCount();
                if ($foundMoreNodesThanMappingExpected) {
                    throw InvalidArgumentForVariadicFunctionException::between($this->getFunctionName(), $this->getMinArgumentCount(), $this->getMaxArgumentCount());
                }

                $expectedNodeIndex = $isNodeMappingASimplePattern ? 0 : $nodeIndex;
                $argumentCountExceedsMappingPatternExpectation = !\array_key_exists($expectedNodeIndex, $nodeMapping);
                if ($argumentCountExceedsMappingPatternExpectation) {
                    throw InvalidArgumentForVariadicFunctionException::unsupportedCombination(
                        $this->getFunctionName(),
                        \count($this->nodes) + 1,
                        'implementation defines fewer node mappings than the actually provided argument count'
                    );
                }

                $this->nodes[] = $parser->{$nodeMapping[$expectedNodeIndex]}(); // @phpstan-ignore-line
                $nodeIndex++;
            }

            $lookaheadType = DoctrineLexer::getLookaheadType($lexer);
        }

        // Final validation ensures all arguments meet requirements, including any special rules in subclass implementations
        $this->validateArguments(...$this->nodes); // @phpstan-ignore-line
    }

    /**
     * @throws InvalidArgumentForVariadicFunctionException
     */
    protected function validateArguments(Node ...$arguments): void
    {
        $minArgumentCount = $this->getMinArgumentCount();
        $maxArgumentCount = $this->getMaxArgumentCount();
        $argumentCount = \count($arguments);

        if ($argumentCount < $minArgumentCount) {
            throw InvalidArgumentForVariadicFunctionException::atLeast($this->getFunctionName(), $this->getMinArgumentCount());
        }

        if ($argumentCount > $maxArgumentCount) {
            throw InvalidArgumentForVariadicFunctionException::between($this->getFunctionName(), $this->getMinArgumentCount(), $this->getMaxArgumentCount());
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
