<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception;

/**
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidFieldNameException extends \InvalidArgumentException
{
    public static function forNonStringValue(string $functionName): self
    {
        return new self(\sprintf(
            '%s() requires a string literal as the field name argument',
            $functionName
        ));
    }
}
