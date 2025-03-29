<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use Doctrine\ORM\Query\Lexer;

/**
 * @internal
 */
final class DoctrineLexer
{
    /**
     * Checks if the Lexer is prior to version 2.0.0.
     *
     * In Lexer versions prior to 2.0.0, the lookahead property is an array,
     * while in 2.0.0+ it's an object.
     */
    public static function isPre200(Lexer $lexer): bool
    {
        // @phpstan-ignore-next-line
        return \is_array($lexer->lookahead);
    }

    /**
     * @return mixed|null
     */
    public static function getLookaheadType(Lexer $lexer)
    {
        if (self::isPre200($lexer)) {
            // @phpstan-ignore-next-line
            return $lexer->lookahead['type'];
        }

        // @phpstan-ignore-next-line
        return $lexer->lookahead?->type;
    }

    /**
     * @return mixed|null
     */
    public static function getTokenValue(Lexer $lexer)
    {
        if (self::isPre200($lexer)) {
            // @phpstan-ignore-next-line
            if ($lexer->token === null) {
                return null;
            }

            // @phpstan-ignore-next-line
            return $lexer->token['value'];
        }

        // @phpstan-ignore-next-line
        return $lexer->token?->value;
    }
}
