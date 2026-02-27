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
        $patterns = $this->getNodeMappingPattern();

        if (\count($patterns) > 1) {
            $resolved = $this->resolvePatternByTokenAnalysis($parser->getLexer(), $patterns);
            if ($resolved !== null) {
                $this->feedParserWithNodesForNodeMappingPattern($parser, $resolved);

                return;
            }
        }

        foreach ($patterns as $pattern) {
            try {
                $this->feedParserWithNodesForNodeMappingPattern($parser, $pattern);

                break;
            } catch (ParserException) {
                // swallow and continue with next pattern
            }
        }
    }

    /**
     * Peeks at tokens ahead of parsing to select the correct pattern when multiple
     * patterns share the same prefix but diverge on argument types.
     *
     * @param array<string> $patterns
     */
    private function resolvePatternByTokenAnalysis(Lexer $lexer, array $patterns): ?string
    {
        $argumentTokenTypes = $this->peekArgumentTokenTypes($lexer);
        $argumentCount = \count($argumentTokenTypes);

        if ($argumentCount === 0) {
            return null;
        }

        $candidates = [];
        foreach ($patterns as $pattern) {
            $nodeMapping = \explode(',', $pattern);
            $patternNodeCount = \count($nodeMapping);

            if ($patternNodeCount < $argumentCount) {
                continue;
            }

            $compatible = true;
            for ($i = 0; $i < $argumentCount; $i++) {
                if (!$this->isTokenCompatibleWithNodeType($argumentTokenTypes[$i], $nodeMapping[$i])) {
                    $compatible = false;

                    break;
                }
            }

            if ($compatible) {
                $candidates[] = $pattern;
            }
        }

        if (\count($candidates) === 1) {
            return $candidates[0];
        }

        return null;
    }

    /**
     * Peeks at tokens to determine the first token type of each function argument.
     * Tracks parenthesis depth to correctly handle nested function calls.
     *
     * @return list<mixed>
     */
    private function peekArgumentTokenTypes(Lexer $lexer): array
    {
        $firstArgumentType = DoctrineLexer::getLookaheadType($lexer);
        if ($firstArgumentType === null) {
            return [];
        }

        $types = [$firstArgumentType];
        $depth = 0;

        $shouldUseLexer = DoctrineOrm::isPre219();
        $commaType = $shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA;
        $openParenthesisType = $shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS;
        $closeParenthesisType = $shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS;

        while (true) {
            $token = $lexer->peek();
            if ($token === null) {
                break;
            }

            $tokenType = \is_array($token) ? $token['type'] : $token->type; // @phpstan-ignore-line

            if ($tokenType === $openParenthesisType) {
                $depth++;
            } elseif ($tokenType === $closeParenthesisType) {
                if ($depth === 0) {
                    break;
                }

                $depth--;
            } elseif ($tokenType === $commaType && $depth === 0) {
                $nextToken = $lexer->peek();
                if ($nextToken !== null) {
                    $types[] = \is_array($nextToken) ? $nextToken['type'] : $nextToken->type; // @phpstan-ignore-line
                }
            }
        }

        $lexer->resetPeek();

        return $types;
    }

    /**
     * Determines if a token type is compatible with a node mapping type.
     *
     * Numeric literals (T_INTEGER/T_FLOAT) are only compatible with ArithmeticPrimary.
     * String literals (T_STRING) are only compatible with StringPrimary.
     * Identifiers, parameters, and function calls are compatible with all node types.
     */
    private function isTokenCompatibleWithNodeType(mixed $tokenType, string $nodeType): bool
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $integerType = $shouldUseLexer ? Lexer::T_INTEGER : TokenType::T_INTEGER;
        $floatType = $shouldUseLexer ? Lexer::T_FLOAT : TokenType::T_FLOAT;
        $stringLiteralType = $shouldUseLexer ? Lexer::T_STRING : TokenType::T_STRING;

        $isNumericToken = ($tokenType === $integerType || $tokenType === $floatType);
        $isStringLiteralToken = ($tokenType === $stringLiteralType);

        if ($isNumericToken && $nodeType === 'StringPrimary') {
            return false;
        }

        return !($isStringLiteralToken && \in_array($nodeType, ['ArithmeticPrimary', 'SimpleArithmeticExpression'], true));
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
                    if ($this->getMinArgumentCount() === $this->getMaxArgumentCount()) {
                        throw InvalidArgumentForVariadicFunctionException::exactCount($this->getFunctionName(), $this->getMinArgumentCount());
                    }

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

        if ($minArgumentCount === $maxArgumentCount && $argumentCount !== $minArgumentCount) {
            throw InvalidArgumentForVariadicFunctionException::exactCount($this->getFunctionName(), $this->getMinArgumentCount());
        }

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
