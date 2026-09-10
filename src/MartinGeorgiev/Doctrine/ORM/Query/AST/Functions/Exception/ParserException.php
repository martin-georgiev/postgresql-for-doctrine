<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

class ParserException extends \RuntimeException
{
    public static function withThrowable(\Throwable $throwable): self
    {
        return new self($throwable->getMessage(), $throwable->getCode(), $throwable);
    }

    public static function forUnparsableArgumentList(string $functionName): self
    {
        return new self(\sprintf(
            'Cannot parse the argument list of %s(). Expected a comma or a closing parenthesis after an argument.',
            $functionName
        ));
    }
}
