<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;

/**
 * Implementation of PostGIS GEOMETRY data type.
 *
 * @see https://postgis.net/docs/using_postgis_dbmanagement.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Geometry extends BaseSpatialType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::GEOMETRY;

    protected function throwInvalidTypeExceptionForDatabase(mixed $value): never
    {
        throw InvalidGeometryForDatabaseException::forInvalidType($value);
    }

    protected function throwInvalidTypeExceptionForPHP(mixed $value): never
    {
        throw InvalidGeometryForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidGeometryForPHPException::forInvalidFormat($value);
    }
}
