<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidJsonFormatException;

/**
 * Handles transformation from PostgreSQL JSON(B) values to PHP values.
 *
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PostgresJsonToPHPArrayTransformer
{
    /**
     * @var string
     */
    private const POSTGRESQL_EMPTY_ARRAY = '{}';

    public static function transformPostgresArrayToPHPArray(string $postgresValue): array
    {
        if ($postgresValue === self::POSTGRESQL_EMPTY_ARRAY) {
            return [];
        }

        $trimmedPostgresArray = \mb_substr($postgresValue, 2, -2);
        $phpArray = \explode('},{', $trimmedPostgresArray);
        foreach ($phpArray as &$item) {
            $item = '{'.$item.'}';
        }

        return $phpArray;
    }

    /**
     * @throws InvalidJsonFormatException When the PostgreSQL value is not a JSON array
     */
    public static function transformPostgresJsonEncodedValueToPHPArray(string $postgresValue): array
    {
        try {
            $transformedValue = \json_decode($postgresValue, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
            if (!\is_array($transformedValue)) {
                throw InvalidJsonFormatException::invalidFormat('the value does not decode to an array');
            }

            return $transformedValue;
        } catch (\JsonException) {
            throw InvalidJsonFormatException::invalidFormat('the value is not decodable JSON');
        }
    }

    /**
     * @throws InvalidJsonFormatException When the PostgreSQL value is not JSON-decodable
     */
    public static function transformPostgresJsonEncodedValueToPHPValue(string $postgresValue): array|bool|float|int|string|null
    {
        try {
            // @phpstan-ignore-next-line
            return \json_decode($postgresValue, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (\JsonException) {
            throw InvalidJsonFormatException::invalidFormat('the value is not decodable JSON');
        }
    }
}
