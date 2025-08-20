<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;

/**
 * Implementation of PostgreSQL array for PostGIS GEOGRAPHY data type.
 *
 * @since 3.5
 */
final class GeographyArray extends SpatialDataArray
{
    protected const TYPE_NAME = 'geography[]';

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if (!$this->isValidArrayItemForDatabase($item)) {
            throw InvalidGeographyForPHPException::forInvalidType($item);
        }

        return (string) $item;
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidGeographyForPHPException::forInvalidType($item);
    }

    protected function createInvalidFormatExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidGeographyForPHPException::forInvalidFormat($item);
    }
}
