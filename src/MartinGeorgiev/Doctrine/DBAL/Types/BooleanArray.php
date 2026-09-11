<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL BOOL[] data type.
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 1.6
 *
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class BooleanArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BOOL_ARRAY;

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

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_scalar($item));

        return (string) $item;
    }

    /**
     * @return array<int, mixed>
     */
    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray(
            $postgresArray,
            preserveStringTypes: true
        );
    }

    public function convertToPHPValue($postgresArray, AbstractPlatform $platform): ?array
    {
        $phpArray = parent::convertToPHPValue($postgresArray, $platform);
        if (!\is_array($phpArray)) {
            return null;
        }

        return \array_map(
            static fn (mixed $item): ?bool => $item === null ? null : $platform->convertFromBoolean($item),
            $phpArray
        );
    }
}
