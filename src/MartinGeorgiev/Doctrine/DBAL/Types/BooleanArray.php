<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Implementation of PostgreSQL BOOL[] data type.
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 1.5.3
 *
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class BooleanArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = 'bool[]';

    public function convertToDatabaseValue($phpArray, AbstractPlatform $platform): ?string
    {
        if (\is_array($phpArray)) {
            $phpArray = $platform->convertBooleansToDatabaseValue($phpArray);
        }

        /*
         * $phpArray type will be checked by parent class
         * @phpstan-ignore-next-line
         */
        return parent::convertToDatabaseValue($phpArray, $platform);
    }

    public function convertToPHPValue($postgresArray, AbstractPlatform $platform): ?array
    {
        $phpArray = parent::convertToPHPValue($postgresArray, $platform);
        if (!\is_array($phpArray)) {
            return null;
        }

        return \array_map(static fn ($value): ?bool => $platform->convertFromBoolean($value), $phpArray);
    }
}
