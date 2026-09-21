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
        return \is_array($lexer->lookahead);
    }

    /**
     * @return mixed|null
     */
    public static function getLookaheadType(Lexer $lexer)
    {
        if (self::isPre200($lexer)) {
            // @phpstan-ignore offsetAccess.nonOffsetAccessible
            return $lexer->lookahead['type'];
        }

        return $lexer->lookahead?->type;
    }

    /**
     * @return mixed|null
     */
    public static function getLookaheadValue(Lexer $lexer)
    {
        if (self::isPre200($lexer)) {
            return $lexer->lookahead['value'] ?? null;
        }

        return $lexer->lookahead?->value;
    }

    /**
     * @return mixed|null
     */
    public static function getTokenValue(Lexer $lexer)
    {
        if (self::isPre200($lexer)) {
            if ($lexer->token === null) {
                return null;
            }

            return $lexer->token['value'];
        }

        return $lexer->token?->value;
    }
}
