<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * Helpers for converting PHP values into PostgreSql JSOn and vice versa
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait JsonTransformer
{
    /**
     * @param mixed $phpValue
     * @return string
     *
     * @throws ConversionException When given value cannot be encoded
     */
    protected function transformToPostgresJson($phpValue)
    {
        $postgresValue = json_encode($phpValue);
        if ($postgresValue === false) {
            throw new ConversionException(sprintf('Value %s can\'t be resolved to valid JSON', var_export($phpValue, true)));
        }

        return $postgresValue;
    }

    /**
     * @param string $postgresValue
     * @return array
     */
    protected function transformFromPostgresJson($postgresValue)
    {
        return json_decode($postgresValue, true);
    }
}
