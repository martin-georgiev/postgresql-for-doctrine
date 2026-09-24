<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\WindowSpecificationTrait;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Renders FUNC_OVER(arg, ..., [PARTITION BY ...] [ORDER BY ...]) as func(arg, ...) OVER ([PARTITION BY ...] [ORDER BY ...]).
 *
 * DQL cannot parse anything after a function's closing parenthesis, so the window specification rides inside the call
 * as the tail of its argument list. Subclasses declare their leading arguments with addNodeMapping() in
 * customizeFunction(), one parser method per position; positions past getMinArgumentCount() are optional.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseWindowFunction extends BaseFunction
{
    use WindowSpecificationTrait;

    /**
     * @var list<Node|string>
     */
    protected array $arguments = [];

    abstract protected function getFunctionName(): string;

    protected function customizeFunction(): void {}

    protected function getMinArgumentCount(): int
    {
        return \count($this->nodesMapping);
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->parseArguments($parser);

        $hasArguments = $this->arguments !== [];
        $isSeparatedFromArguments = $hasArguments && $parser->getLexer()->isNextToken($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
        if ($isSeparatedFromArguments) {
            $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
        }

        if (!$hasArguments || $isSeparatedFromArguments) {
            $this->parseWindowSpecification($parser);
        }

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    private function parseArguments(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $maxArgumentCount = \count($this->nodesMapping);

        while ($this->isAnotherArgumentNext($parser->getLexer())) {
            $argumentCount = \count($this->arguments);
            if ($argumentCount === $maxArgumentCount) {
                throw $this->createArgumentCountException();
            }

            if ($argumentCount > 0) {
                $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
            }

            $argument = $parser->{$this->nodesMapping[$argumentCount]}();
            \assert($argument instanceof Node || \is_string($argument));
            $this->arguments[] = $argument;
        }

        if (\count($this->arguments) < $this->getMinArgumentCount()) {
            throw $this->createArgumentCountException();
        }
    }

    private function isAnotherArgumentNext(Lexer $lexer): bool
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        if ($this->arguments === []) {
            return !$lexer->isNextToken($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS)
                && !$this->isWindowSpecificationNext($lexer);
        }

        return $lexer->isNextToken($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA)
            && !$this->isWindowSpecificationNext($lexer, 1);
    }

    private function createArgumentCountException(): InvalidArgumentForVariadicFunctionException
    {
        $minArgumentCount = $this->getMinArgumentCount();
        $maxArgumentCount = \count($this->nodesMapping);

        if ($minArgumentCount === $maxArgumentCount) {
            return InvalidArgumentForVariadicFunctionException::exactCount($this->getFunctionName(), $minArgumentCount);
        }

        return InvalidArgumentForVariadicFunctionException::between($this->getFunctionName(), $minArgumentCount, $maxArgumentCount);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $arguments = \array_map($sqlWalker->walkSimpleArithmeticExpression(...), $this->arguments);

        return \sprintf(
            '%s(%s) OVER (%s)',
            $this->getFunctionName(),
            \implode(', ', $arguments),
            $this->getWindowSpecificationSql($sqlWalker)
        );
    }
}
