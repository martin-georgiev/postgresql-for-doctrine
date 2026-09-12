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
class InvalidXmlPiTargetException extends \InvalidArgumentException
{
    public static function forEmptyTarget(string $functionName): self
    {
        return new self(\sprintf(
            '%s() requires a non-empty string literal as the target argument',
            $functionName
        ));
    }
}
