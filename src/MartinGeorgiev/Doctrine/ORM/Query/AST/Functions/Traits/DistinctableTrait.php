<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineOrm;

trait DistinctableTrait
{
    protected bool $isDistinct = false;

    protected function parseDistinctClause(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();
        $lexer = $parser->getLexer();

        if ($lexer->isNextToken($shouldUseLexer ? Lexer::T_DISTINCT : TokenType::T_DISTINCT)) {
            $parser->match($shouldUseLexer ? Lexer::T_DISTINCT : TokenType::T_DISTINCT);
            $this->isDistinct = true;
        }
    }

    protected function getOptionalDistinctClause(): string
    {
        return $this->isDistinct ? 'DISTINCT ' : '';
    }
}
