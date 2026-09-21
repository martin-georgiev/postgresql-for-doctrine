<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;

/**
 * Implementation of PostgreSQL array for PostGIS GEOGRAPHY data type.
 *
 * @see https://postgis.net/docs/using_postgis_dbmanagement.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class GeographyArray extends SpatialDataArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::GEOGRAPHY_ARRAY;

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidGeographyForDatabaseException::forInvalidType($item);
    }

    protected function throwInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidGeographyForPHPException::forInvalidType($item);
    }

    protected function throwInvalidFormatExceptionForPHP(mixed $item): never
    {
        throw InvalidGeographyForPHPException::forInvalidFormat($item);
    }
}
