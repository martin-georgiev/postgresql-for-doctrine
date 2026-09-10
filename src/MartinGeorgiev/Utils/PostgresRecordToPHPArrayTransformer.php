<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidRecordFormatException;

/**
 * Handles transformation from a PostgreSQL record literal to its raw field strings.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PostgresRecordToPHPArrayTransformer
{
    /**
     * Splits a PostgreSQL record literal into its raw field strings, where null marks an unquoted empty field.
     *
     * @return array<int, string|null>
     *
     * @throws InvalidRecordFormatException
     */
    public static function transformPostgresRecordToPHPArray(string $literal): array
    {
        $trimmed = \trim($literal);
        if (!\str_starts_with($trimmed, '(') || !\str_ends_with($trimmed, ')')) {
            throw InvalidRecordFormatException::invalidFormat('a record literal must be wrapped in parentheses');
        }

        $body = \substr($trimmed, 1, -1);
        $fields = [];
        $current = '';
        $wasQuoted = false;
        $inQuotes = false;
        $length = \strlen($body);

        for ($index = 0; $index < $length; $index++) {
            $character = $body[$index];

            if ($character === '\\') {
                if ($index + 1 >= $length) {
                    throw InvalidRecordFormatException::invalidFormat('the literal ends with a dangling escape character');
                }

                $current .= $body[++$index];

                continue;
            }

            if ($character === '"') {
                // A doubled quote inside a quoted section is a literal quote, not the end of the section
                if ($inQuotes && $index + 1 < $length && $body[$index + 1] === '"') {
                    $current .= '"';
                    $index++;

                    continue;
                }

                $inQuotes = !$inQuotes;
                $wasQuoted = true;

                continue;
            }

            if ($character === ',' && !$inQuotes) {
                $fields[] = $wasQuoted || $current !== '' ? $current : null;
                $current = '';
                $wasQuoted = false;

                continue;
            }

            // The outer parenthesis was already stripped, so a bare one here means the record ended early and the
            // rest is junk. PostgreSQL rejects those too, while accepting a bare opening parenthesis inside a field.
            if ($character === ')' && !$inQuotes) {
                throw InvalidRecordFormatException::invalidFormat('a closing parenthesis appears outside a quoted field');
            }

            $current .= $character;
        }

        if ($inQuotes) {
            throw InvalidRecordFormatException::invalidFormat('a quoted field is left unterminated');
        }

        $fields[] = $wasQuoted || $current !== '' ? $current : null;

        return $fields;
    }
}
