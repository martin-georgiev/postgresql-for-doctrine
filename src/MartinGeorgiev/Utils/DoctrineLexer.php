<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use Doctrine\ORM\Query\Lexer;

/**
 * @internal
 */
final class DoctrineLexer
{
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
}
