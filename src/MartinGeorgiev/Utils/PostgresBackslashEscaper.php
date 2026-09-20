<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

/**
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PostgresBackslashEscaper
{
    public static function escape(string $value): string
    {
        return \str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
    }

    /**
     * A backslash escapes whatever follows it, not only a backslash or a quote: array_in reads `{"a\xb"}` as `axb`.
     * One at the very end escapes nothing and stays.
     */
    public static function unescape(string $value): string
    {
        $result = '';
        $length = \strlen($value);
        $position = 0;

        while ($position < $length) {
            $escapesTheNextCharacter = $value[$position] === '\\' && $position + 1 < $length;
            $result .= $escapesTheNextCharacter ? $value[$position + 1] : $value[$position];
            $position += $escapesTheNextCharacter ? 2 : 1;
        }

        return $result;
    }
}
