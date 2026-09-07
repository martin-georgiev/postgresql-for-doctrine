<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube as CubeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCubeException;

/**
 * Implementation of PostgreSQL cube extension type.
 *
 * Multidimensional cube, either a point or a box spanned by two opposite
 * corners. Requires the cube extension.
 *
 * @see https://www.postgresql.org/docs/18/cube.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Cube extends BaseType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::CUBE;

    /**
     * @throws InvalidCubeForDatabaseException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof CubeValueObject) {
            throw InvalidCubeForDatabaseException::forInvalidType($value);
        }

        return (string) $value;
    }

    /**
     * @throws InvalidCubeForPHPException
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?CubeValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidCubeForPHPException::forInvalidType($value);
        }

        try {
            return CubeValueObject::fromString($value);
        } catch (InvalidCubeException) {
            throw InvalidCubeForPHPException::forInvalidFormat($value);
        }
    }
}
