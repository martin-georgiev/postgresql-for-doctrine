<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonItemForPHPException;
use MartinGeorgiev\Utils\Exception\InvalidJsonFormatException;
use MartinGeorgiev\Utils\PostgresJsonToPHPArrayTransformer;

/**
 * Helpers for converting PHP values into PostgreSQL JSON and vice versa.
 *
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait JsonTransformer
{
    abstract protected function throwInvalidJsonValueException(mixed $phpValue): never;

    /**
     * @param mixed $phpValue Value must be suitable for JSON encoding
     */
    protected function transformToPostgresJson(mixed $phpValue): string
    {
        try {
            return \json_encode($phpValue, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $this->throwInvalidJsonValueException($phpValue);
        }
    }

    protected function transformFromPostgresJson(string $postgresValue): array|bool|float|int|string|null
    {
        try {
            return PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPValue($postgresValue);
        } catch (InvalidJsonFormatException) {
            throw InvalidJsonItemForPHPException::forInvalidType($postgresValue);
        }
    }
}
