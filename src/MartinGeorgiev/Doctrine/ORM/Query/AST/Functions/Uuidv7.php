<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;
use MartinGeorgiev\Utils\DoctrineLexer;
use MartinGeorgiev\Utils\DoctrineOrm;

/**
 * Implementation of PostgreSQL UUIDV7().
 *
 * Generates a time-based UUID v7. The optional shift argument (PostgreSQL interval)
 * shifts the computed timestamp by the given interval.
 *
 * @see https://www.postgresql.org/docs/18/functions-uuid.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT UUIDV7() FROM Entity e"
 * @example Using it in DQL with shift: "SELECT UUIDV7('1 hour') FROM Entity e"
 */
class Uuidv7 extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('uuidv7(%s)');
    }

    public function parse(Parser $parser): void
    {
        $shouldUseLexer = DoctrineOrm::isPre219();

        $this->customizeFunction();

        $parser->match($shouldUseLexer ? Lexer::T_IDENTIFIER : TokenType::T_IDENTIFIER);
        $parser->match($shouldUseLexer ? Lexer::T_OPEN_PARENTHESIS : TokenType::T_OPEN_PARENTHESIS);

        $closeParenthesisType = $shouldUseLexer ? Lexer::T_CLOSE_PARENTHESIS : TokenType::T_CLOSE_PARENTHESIS;
        if (DoctrineLexer::getLookaheadType($parser->getLexer()) === $closeParenthesisType) {
            $this->setFunctionPrototype('uuidv7()');
        } else {
            $this->nodes[] = $parser->StringPrimary();
        }

        $parser->match($closeParenthesisType);
    }
}
