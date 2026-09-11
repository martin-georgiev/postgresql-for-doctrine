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
