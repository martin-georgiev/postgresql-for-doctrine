<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\DistinctableTrait;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\OrderableTrait;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL ARRAY_AGG().
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayAgg extends BaseFunction
{
    use OrderableTrait;
    use DistinctableTrait;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_agg(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $this->parseDistinctClause($parser);
        $this->expression = $parser->StringPrimary();

        $this->parseOrderByClause($parser);

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [
            $this->getOptionalDistinctClause(),
            $this->expression->dispatch($sqlWalker),
            $this->getOptionalOrderByClause($sqlWalker),
        ];

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
