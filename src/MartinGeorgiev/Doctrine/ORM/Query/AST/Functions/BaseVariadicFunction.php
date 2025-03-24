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
    protected string $commonNodeMapping = 'StringPrimary';

    /**
     * @throws ParserException
     */
    protected function feedParserWithNodes(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        try {
            // @phpstan-ignore-next-line
            $this->nodes[] = $parser->{$this->commonNodeMapping}();
            $lookaheadType = DoctrineLexer::getLookaheadType($lexer);
            if ($lookaheadType === null) {
                throw ParserException::missingLookaheadType();
            }
        } catch (\Throwable $throwable) {
            throw ParserException::withThrowable($throwable);
        }

        $shouldUseLexer = DoctrineOrm::isPre219();

        while (($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS) !== $lookaheadType) {
            if (($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA) === $lookaheadType) {
                $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
                // @phpstan-ignore-next-line
                $this->nodes[] = $parser->{$this->commonNodeMapping}();
            }

            $lookaheadType = DoctrineLexer::getLookaheadType($lexer);
        }

        $this->validateArguments($this->nodes);
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
     * @param mixed[] $arguments The array of arguments to validate
     *
     * @throws InvalidArgumentForVariadicFunctionException
     */
    abstract protected function validateArguments(array $arguments): void;
}
