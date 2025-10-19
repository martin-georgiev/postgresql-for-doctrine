<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

class ParserException extends \RuntimeException
{
    public static function missingLookaheadType(): self
    {
        return new self("The parser's 'lookahead' property is not populated with a type");
    }

    public static function withThrowable(\Throwable $throwable): self
    {
        return new self($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}
