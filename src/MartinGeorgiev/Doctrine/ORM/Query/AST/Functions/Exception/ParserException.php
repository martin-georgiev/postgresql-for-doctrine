<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

/**
 * @since 2.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ParserException extends \RuntimeException
{
    public static function withThrowable(\Throwable $throwable): self
    {
        return new self($throwable->getMessage(), $throwable->getCode(), $throwable);
    }

    public static function forNonAggregateArgument(string $functionName, string $argumentName): self
    {
        return new self(\sprintf('%s() requires an aggregate as its first argument, %s() given', $functionName, $argumentName));
    }

    public static function forUnparsableArgumentList(string $functionName): self
    {
        return new self(\sprintf(
            'Cannot parse the argument list of %s(). Expected a comma or a closing parenthesis after an argument.',
            $functionName
        ));
    }
}
