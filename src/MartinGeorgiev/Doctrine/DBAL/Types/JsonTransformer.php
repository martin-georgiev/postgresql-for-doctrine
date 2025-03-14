<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * Helpers for converting PHP values into PostgreSQL JSON and vice versa.
 *
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait JsonTransformer
{
    /**
     * @param mixed $phpValue Value must be suitable for JSON encoding
     *
     * @throws ConversionException When given value cannot be encoded
     */
    protected function transformToPostgresJson(mixed $phpValue): string
    {
        try {
            $postgresValue = \json_encode($phpValue, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new ConversionException(\sprintf("Value %s can't be resolved to valid JSON", \var_export($phpValue, true)));
        }

        return $postgresValue;
    }

    protected function transformFromPostgresJson(string $postgresValue): null|array|bool|float|int|string
    {
        // @phpstan-ignore-next-line
        return \json_decode($postgresValue, true, 512, JSON_THROW_ON_ERROR);
    }
}
