<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

class InvalidArgumentForVariadicFunctionException extends \InvalidArgumentException
{
    public static function exactCount(string $functionName, int $expected): self
    {
        return new self(\sprintf(
            '%s() requires exactly %d argument%s',
            $functionName,
            $expected,
            $expected === 1 ? '' : 's'
        ));
    }

    public static function atLeast(string $functionName, int $min): self
    {
        return new self(\sprintf(
            '%s() requires at least %d argument%s',
            $functionName,
            $min,
            $min === 1 ? '' : 's'
        ));
    }

    public static function between(string $functionName, int $min, int $max): self
    {
        return new self(\sprintf(
            '%s() requires between %d and %d arguments',
            $functionName,
            $min,
            $max
        ));
    }

    public static function evenNumber(string $functionName): self
    {
        return new self(\sprintf(
            '%s() requires an even number of arguments',
            $functionName
        ));
    }
}
