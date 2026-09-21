<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\UlidValidationTrait;

/**
 * Implementation of the ulid[] type from the pgx_ulid PostgreSQL extension.
 *
 * Requires the pgx_ulid extension. PostgreSQL outputs the canonical uppercase form on
 * retrieval; items are normalized to uppercase on both conversions so round-trips are stable.
 *
 * @see https://github.com/pksunkara/pgx_ulid
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class UlidArray extends BaseArray
{
    use UlidValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::ULID_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_string($item)); // validated by isValidArrayItemForDatabase

        return '"'.\strtoupper($item).'"';
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return $this->isValidUlid($item);
    }

    protected function throwInvalidArrayFormatException(string $postgresArray): never
    {
        throw InvalidUlidArrayItemForPHPException::forInvalidFormat($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidUlidArrayItemForPHPException::forInvalidType($item);
        }

        if (!$this->isValidUlid($item)) {
            throw InvalidUlidArrayItemForPHPException::forInvalidFormat($item);
        }

        return \strtoupper($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidUlidArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidUlidArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
