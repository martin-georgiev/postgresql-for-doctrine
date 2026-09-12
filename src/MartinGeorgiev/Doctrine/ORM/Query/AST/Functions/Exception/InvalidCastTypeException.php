<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

/**
 * Extends \InvalidArgumentException because this is a malformed-DQL error raised while parsing,
 * not a per-value conversion failure.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidCastTypeException extends \InvalidArgumentException
{
    public static function forInvalidTypeName(string $typeName): self
    {
        return new self(\sprintf(
            'CAST() target type must be a plain type name, %s given',
            \var_export($typeName, true)
        ));
    }

    public static function forInvalidTypeParameter(string $parameter): self
    {
        return new self(\sprintf(
            'CAST() target type parameters must be non-negative integers, %s given',
            \var_export($parameter, true)
        ));
    }

    public static function forTooManyTypeParameters(int $count): self
    {
        return new self(\sprintf(
            'CAST() target type accepts at most two parameters, %d given',
            $count
        ));
    }
}
