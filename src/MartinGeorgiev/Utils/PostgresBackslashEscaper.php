<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

/**
 * @internal
 *
 * @since 4.8.1
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
        if (!\str_contains($value, '\\')) {
            return $value;
        }

        $result = '';
        $length = \strlen($value);
        $position = 0;

        while ($position < $length) {
            $runLength = \strcspn($value, '\\', $position);
            $result .= \substr($value, $position, $runLength);
            $position += $runLength;
            if ($position >= $length) {
                break;
            }

            $escapesTheNextCharacter = $position + 1 < $length;
            $result .= $escapesTheNextCharacter ? $value[$position + 1] : '\\';
            $position += 2;
        }

        return $result;
    }
}
