<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;

/**
 * Implementation of PostGIS GEOGRAPHY data type.
 *
 * @see https://postgis.net/docs/using_postgis_dbmanagement.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Geography extends BaseSpatialType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::GEOGRAPHY;

    protected function throwInvalidTypeExceptionForDatabase(mixed $value): never
    {
        throw InvalidGeographyForDatabaseException::forInvalidType($value);
    }

    protected function throwInvalidTypeExceptionForPHP(mixed $value): never
    {
        throw InvalidGeographyForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidGeographyForPHPException::forInvalidFormat($value);
    }
}
