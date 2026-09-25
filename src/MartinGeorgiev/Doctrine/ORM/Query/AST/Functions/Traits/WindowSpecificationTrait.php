<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Parses and renders the body of an OVER (...) clause: [PARTITION BY expression, ...] [ORDER BY item, ...] [frame clause].
 *
 * DQL has no PARTITION keyword, so it arrives as an identifier and only counts as one when BY follows it. Likewise, the
 * frame modes ROWS, RANGE and GROUPS arrive as identifiers and only count as one when BETWEEN or a frame bound follows.
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
     * @var list<Node|string>
     */
    protected array $frameClauseParts = [];

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

        if ($this->isFrameClauseStart($firstToken, $secondToken)) {
            return true;
        }

        return $this->isKeywordIdentifier($firstToken, 'PARTITION')
            && $this->getWindowTokenType($secondToken) === ($shouldUseLexer ? Lexer::T_BY : TokenType::T_BY);
    }

    protected function parseWindowSpecification(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        if ($this->isKeywordIdentifier($lexer->lookahead, 'PARTITION')) {
            $this->parsePartitionByClause($parser);
        }

        $this->parseOrderByClause($parser);

        $tokenAfterFrameMode = $lexer->peek();
        $lexer->resetPeek();
        if ($this->isFrameClauseStart($lexer->lookahead, $tokenAfterFrameMode)) {
            $this->parseFrameClause($parser);
        }
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

        if ($this->frameClauseParts !== []) {
            $frameClauseParts = \array_map(
                static fn (Node|string $part): string => $part instanceof Node ? $part->dispatch($sqlWalker) : $part,
                $this->frameClauseParts
            );
            $clauses[] = \implode(' ', $frameClauseParts);
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

    private function isFrameClauseStart(mixed $modeToken, mixed $tokenAfterMode): bool
    {
        if ($this->getFrameMode($modeToken) === null) {
            return false;
        }

        $shouldUseLexer = DoctrineOrm::isPre219();
        $tokenTypesStartingTheExtent = $shouldUseLexer ? [Lexer::T_BETWEEN, Lexer::T_INTEGER, Lexer::T_FLOAT, Lexer::T_STRING, Lexer::T_INPUT_PARAMETER] : [TokenType::T_BETWEEN, TokenType::T_INTEGER, TokenType::T_FLOAT, TokenType::T_STRING, TokenType::T_INPUT_PARAMETER];

        return \in_array($this->getWindowTokenType($tokenAfterMode), $tokenTypesStartingTheExtent, true)
            || $this->isKeywordIdentifier($tokenAfterMode, 'UNBOUNDED')
            || $this->isKeywordIdentifier($tokenAfterMode, 'CURRENT');
    }

    private function getFrameMode(mixed $token): ?string
    {
        foreach (['ROWS', 'RANGE', 'GROUPS'] as $frameMode) {
            if ($this->isKeywordIdentifier($token, $frameMode)) {
                return $frameMode;
            }
        }

        return null;
    }

    private function parseFrameClause(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        $frameMode = $this->getFrameMode($lexer->lookahead);
        \assert($frameMode !== null);
        $this->matchKeywordIdentifier($parser, $frameMode);
        $this->frameClauseParts[] = $frameMode;

        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_BETWEEN : TokenType::T_BETWEEN)) {
            $parser->match($shouldUseLexer ? Lexer::T_BETWEEN : TokenType::T_BETWEEN);
            $this->frameClauseParts[] = 'BETWEEN';
            $this->parseFrameBound($parser);

            $parser->match($shouldUseLexer ? Lexer::T_AND : TokenType::T_AND);
            $this->frameClauseParts[] = 'AND';
        }

        $this->parseFrameBound($parser);

        if ($this->isKeywordIdentifier($lexer->lookahead, 'EXCLUDE')) {
            $this->parseFrameExclusion($parser);
        }
    }

    private function parseFrameBound(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        if ($this->isKeywordIdentifier($lexer->lookahead, 'CURRENT')) {
            $this->matchKeywordIdentifier($parser, 'CURRENT');
            $this->matchKeywordIdentifier($parser, 'ROW');
            $this->frameClauseParts[] = 'CURRENT ROW';

            return;
        }

        $literalOffsetTokenTypes = $shouldUseLexer ? [Lexer::T_INTEGER, Lexer::T_FLOAT, Lexer::T_STRING] : [TokenType::T_INTEGER, TokenType::T_FLOAT, TokenType::T_STRING];

        if ($this->isKeywordIdentifier($lexer->lookahead, 'UNBOUNDED')) {
            $this->matchKeywordIdentifier($parser, 'UNBOUNDED');
            $this->frameClauseParts[] = 'UNBOUNDED';
        } elseif ($lexer->isNextToken($shouldUseLexer ? Lexer::T_INPUT_PARAMETER : TokenType::T_INPUT_PARAMETER)) {
            $this->frameClauseParts[] = $parser->InputParameter();
        } elseif (\in_array(DoctrineLexer::getLookaheadType($lexer), $literalOffsetTokenTypes, true)) {
            $this->frameClauseParts[] = $parser->Literal();
        } else {
            $parser->syntaxError('UNBOUNDED, CURRENT ROW or an offset');
        }

        foreach (['PRECEDING', 'FOLLOWING'] as $direction) {
            if ($this->isKeywordIdentifier($lexer->lookahead, $direction)) {
                $this->matchKeywordIdentifier($parser, $direction);
                $this->frameClauseParts[] = $direction;

                return;
            }
        }

        $parser->syntaxError('PRECEDING or FOLLOWING');
    }

    private function parseFrameExclusion(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        $this->matchKeywordIdentifier($parser, 'EXCLUDE');

        // GROUP is the one exclusion keyword DQL knows, so it arrives as its own token rather than as an identifier.
        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_GROUP : TokenType::T_GROUP)) {
            $parser->match($shouldUseLexer ? Lexer::T_GROUP : TokenType::T_GROUP);
            $this->frameClauseParts[] = 'EXCLUDE GROUP';

            return;
        }

        if ($this->isKeywordIdentifier($lexer->lookahead, 'CURRENT')) {
            $this->matchKeywordIdentifier($parser, 'CURRENT');
            $this->matchKeywordIdentifier($parser, 'ROW');
            $this->frameClauseParts[] = 'EXCLUDE CURRENT ROW';

            return;
        }

        if ($this->isKeywordIdentifier($lexer->lookahead, 'TIES')) {
            $this->matchKeywordIdentifier($parser, 'TIES');
            $this->frameClauseParts[] = 'EXCLUDE TIES';

            return;
        }

        if ($this->isKeywordIdentifier($lexer->lookahead, 'NO')) {
            $this->matchKeywordIdentifier($parser, 'NO');
            $this->matchKeywordIdentifier($parser, 'OTHERS');
            $this->frameClauseParts[] = 'EXCLUDE NO OTHERS';

            return;
        }

        $parser->syntaxError('CURRENT ROW, GROUP, TIES or NO OTHERS');
    }

    private function matchKeywordIdentifier(Parser $parser, string $keyword): void
    {
        if (!$this->isKeywordIdentifier($parser->getLexer()->lookahead, $keyword)) {
            $parser->syntaxError($keyword);
        }

        $parser->match(DoctrineOrm::isPre219() ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
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

        $value = DoctrineLexer::getTokenField($token, 'value');

        return \is_string($value) && \strtoupper($value) === $keyword;
    }

    private function getWindowTokenType(mixed $token): mixed
    {
        return DoctrineLexer::getTokenField($token, 'type');
    }
}
