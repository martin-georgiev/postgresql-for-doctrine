<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;

/**
 * Implementation of PostgreSQL array for PostGIS GEOGRAPHY data type.
 *
 * @since 3.5
 */
final class GeographyArray extends SpatialDataArray
{
    protected const TYPE_NAME = Type::GEOGRAPHY_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        return (string) $this->getValidatedArrayItem($item);
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
