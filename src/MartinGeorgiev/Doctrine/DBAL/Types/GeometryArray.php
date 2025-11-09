<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;

/**
 * Implementation of PostgreSQL array for PostGIS GEOMETRY data type.
 *
 * @since 3.5
 */
final class GeometryArray extends SpatialDataArray
{
    protected const TYPE_NAME = 'geometry[]';

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        return (string) $this->getValidatedArrayItem($item);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidGeometryForPHPException::forInvalidType($item);
    }

    protected function createInvalidFormatExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidGeometryForPHPException::forInvalidFormat($item);
    }
}
