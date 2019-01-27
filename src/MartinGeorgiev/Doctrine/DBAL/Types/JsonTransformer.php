<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * Helpers for converting PHP values into PostgreSql JSOn and vice versa.
 *
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait JsonTransformer
{
    /**
     * @param mixed $phpValue Value bus be suitable for JSON encoding
     *
     * @throws ConversionException When given value cannot be encoded
     */
    protected function transformToPostgresJson($phpValue): string
    {
        $postgresValue = \json_encode($phpValue);
        if ($postgresValue === false) {
            throw new ConversionException(sprintf('Value %s can\'t be resolved to valid JSON', var_export($phpValue, true)));
        }

        return $postgresValue;
    }

    protected function transformFromPostgresJson(string $postgresValue): array
    {
        return \json_decode($postgresValue, true);
    }
}
