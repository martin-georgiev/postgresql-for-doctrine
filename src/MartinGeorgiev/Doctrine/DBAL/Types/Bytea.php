<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesForPHPException;

/**
 * Implementation of PostgreSQL bytea binary data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-binary.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Bytea extends BaseType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BYTEA;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBytesForDatabaseException::forInvalidType($value);
        }

        return '\\x'.\bin2hex($value);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_resource($value)) {
            \rewind($value);
            $content = \stream_get_contents($value);

            return $content !== false && $content !== '' ? $content : null;
        }

        if (!\is_string($value)) {
            throw InvalidBytesForPHPException::forInvalidType($value);
        }

        if (!\str_starts_with($value, '\\x')) {
            throw InvalidBytesForPHPException::forInvalidFormat($value);
        }

        $decoded = @\hex2bin(\substr($value, 2));

        if ($decoded === false) {
            throw InvalidBytesForPHPException::forInvalidFormat($value);
        }

        return $decoded;
    }
}
