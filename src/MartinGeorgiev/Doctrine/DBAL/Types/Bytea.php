<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidByteaForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidByteaForPHPException;

/**
 * Implementation of PostgreSQL BYTEA data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-binary.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Bytea extends BaseType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BYTEA;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return \strtoupper(self::TYPE_NAME);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_resource($value)) {
            \rewind($value);
            $value = \stream_get_contents($value);

            if ($value === false) {
                return null;
            }
        }

        if (!\is_string($value)) {
            throw InvalidByteaForPHPException::forInvalidType($value);
        }

        if (\str_starts_with($value, '\\x')) {
            $hex = \substr($value, 2);

            if ($hex === '') {
                return '';
            }

            if (!\ctype_xdigit($hex) || \strlen($hex) % 2 !== 0) {
                throw InvalidByteaForPHPException::forInvalidHexFormat($value);
            }

            $binary = \hex2bin($hex);

            if ($binary === false) {
                throw InvalidByteaForPHPException::forInvalidHexFormat($value);
            }

            return $binary;
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidByteaForDatabaseException::forInvalidType($value);
        }

        return '\\x'.\bin2hex($value);
    }
}
