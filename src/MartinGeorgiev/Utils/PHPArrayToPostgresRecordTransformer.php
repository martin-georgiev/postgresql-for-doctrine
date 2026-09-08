<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

/**
 * Handles transformation from already-converted field strings to a PostgreSQL record literal.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PHPArrayToPostgresRecordTransformer
{
    /**
     * PostgreSQL quotes a record field only when leaving it bare would be ambiguous: when it is empty,
     * or when it holds a delimiter, a quote, a backslash or whitespace. Matching that rule exactly keeps
     * what this writes byte-identical to what PostgreSQL hands back for the same value.
     *
     * @var string
     */
    private const FIELD_NEEDS_QUOTING_PATTERN = '/[(),"\\\\\s]/';

    /**
     * @param array<int, string|null> $fields null marks a NULL field, which PostgreSQL writes as nothing at all
     */
    public static function transformPHPArrayToPostgresRecord(array $fields): string
    {
        $encoded = \array_map(
            static function (?string $field): string {
                if ($field === null) {
                    return '';
                }

                if ($field !== '' && \preg_match(self::FIELD_NEEDS_QUOTING_PATTERN, $field) !== 1) {
                    return $field;
                }

                return '"'.\str_replace(['\\', '"'], ['\\\\', '""'], $field).'"';
            },
            $fields
        );

        return '('.\implode(',', $encoded).')';
    }
}
