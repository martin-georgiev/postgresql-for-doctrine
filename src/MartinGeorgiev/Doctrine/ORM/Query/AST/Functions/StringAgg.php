<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\DistinctableTrait;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\OrderableTrait;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL STRING_AGG().
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class StringAgg extends BaseFunction
{
    use OrderableTrait;
    use DistinctableTrait;

    private Node $delimiter;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('string_agg(%s%s, %s%s)');
        $this->addNodeMapping('StringPrimary');
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

        $parser->match($shouldUseLexer ? Lexer::T_COMMA : TokenType::T_COMMA);

        $this->delimiter = $parser->StringPrimary();
        $this->parseOrderByClause($parser);

        $parser->match($shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [
            $this->getOptionalDistinctClause(),
            $this->expression->dispatch($sqlWalker),
            $this->delimiter->dispatch($sqlWalker),
            $this->getOptionalOrderByClause($sqlWalker),
        ];

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
