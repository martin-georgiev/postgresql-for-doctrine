<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Parses and renders the body of an OVER (...) clause: [PARTITION BY expression, ...] [ORDER BY item, ...].
 *
 * DQL has no PARTITION keyword, so it arrives as an identifier and only counts as one when BY follows it.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait WindowSpecificationTrait
{
    use OrderableTrait;

    /**
     * @var list<Node|string>
     */
    protected array $partitionByExpressions = [];

    /**
     * @param int $tokensToSkip how many tokens past the lookahead the specification would start
     */
    protected function isWindowSpecificationNext(Lexer $lexer, int $tokensToSkip = 0): bool
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $tokens = [$lexer->lookahead];
        for ($i = 0; $i <= $tokensToSkip; $i++) {
            $tokens[] = $lexer->peek();
        }

        $lexer->resetPeek();

        $firstToken = $tokens[$tokensToSkip];
        $secondToken = $tokens[$tokensToSkip + 1];
        if ($this->getWindowTokenType($firstToken) === ($shouldUseLexer ? Lexer::T_ORDER : TokenType::T_ORDER)) {
            return true;
        }

        return $this->isKeywordIdentifier($firstToken, 'PARTITION')
            && $this->getWindowTokenType($secondToken) === ($shouldUseLexer ? Lexer::T_BY : TokenType::T_BY);
    }

    protected function parseWindowSpecification(Parser $parser): void
    {
        if ($this->isKeywordIdentifier($parser->getLexer()->lookahead, 'PARTITION')) {
            $this->parsePartitionByClause($parser);
        }

        $this->parseOrderByClause($parser);
    }

    protected function getWindowSpecificationSql(SqlWalker $sqlWalker): string
    {
        $clauses = [];

        if ($this->partitionByExpressions !== []) {
            // SimpleArithmeticExpression() may hand back a bare identifier string, which dispatch() cannot render.
            $partitionByExpressions = \array_map($sqlWalker->walkSimpleArithmeticExpression(...), $this->partitionByExpressions);
            $clauses[] = 'PARTITION BY '.\implode(', ', $partitionByExpressions);
        }

        if ($this->orderByClause instanceof OrderByClause) {
            // Walking the items instead of the clause keeps Doctrine from appending the #[OrderBy] columns of a
            // fetch-joined collection, which belong to the outer query and not to the window.
            $orderByItems = \array_map($sqlWalker->walkOrderByItem(...), $this->orderByClause->orderByItems);
            $clauses[] = 'ORDER BY '.\implode(', ', $orderByItems);
        }

        return \implode(' ', $clauses);
    }

    private function parsePartitionByClause(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_BY : TokenType::T_BY);

        $this->partitionByExpressions[] = $parser->SimpleArithmeticExpression();
        while ($lexer->isNextToken($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA)) {
            $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);
            $this->partitionByExpressions[] = $parser->SimpleArithmeticExpression();
        }
    }

    /**
     * Matches a window keyword DQL does not know, which the lexer therefore hands over as an identifier.
     */
    private function isKeywordIdentifier(mixed $token, string $keyword): bool
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        if ($this->getWindowTokenType($token) !== ($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER)) {
            return false;
        }

        $value = $this->readWindowTokenProperty($token, 'value');

        return \is_string($value) && \strtoupper($value) === $keyword;
    }

    private function getWindowTokenType(mixed $token): mixed
    {
        return $this->readWindowTokenProperty($token, 'type');
    }

    private function readWindowTokenProperty(mixed $token, string $property): mixed
    {
        return \is_object($token) && \property_exists($token, $property) ? $token->{$property} : null;
    }
}
