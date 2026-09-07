<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidForPHPException;

/**
 * Implementation of the ulid type from the pgx_ulid PostgreSQL extension.
 *
 * ULIDs are 26-character Crockford base32 identifiers stored as 128-bit binary values.
 * Requires the pgx_ulid extension. PostgreSQL outputs the canonical uppercase form on
 * retrieval; values are normalized to uppercase on both conversions so round-trips are stable.
 *
 * @see https://github.com/pksunkara/pgx_ulid
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Ulid extends BaseType
{
    // Anchored with \z rather than $, which would also match before a trailing newline.
    /**
     * @var string
     */
    private const ULID_REGEX = '/^[0-7][0-9A-HJKMNP-TV-Z]{25}\z/i';

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::ULID;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidUlidForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidUlid($value)) {
            throw InvalidUlidForDatabaseException::forInvalidFormat($value);
        }

        return \strtoupper($value);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidUlidForPHPException::forInvalidType($value);
        }

        if (!$this->isValidUlid($value)) {
            throw InvalidUlidForPHPException::forInvalidFormat($value);
        }

        return \strtoupper($value);
    }

    private function isValidUlid(string $value): bool
    {
        return (bool) \preg_match(self::ULID_REGEX, $value);
    }
}
