<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesArrayItemForPHPException;

/**
 * Implementation of PostgreSQL bytea[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-binary.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ByteaArray extends BaseStringArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BYTEA_ARRAY;

    /**
     * @var string
     */
    private const ESCAPE_FORMAT_PATTERN = '/\A(?:[^\\\\]|\\\\\\\\|\\\\[0-3][0-7]{2})*\z/s';

    /**
     * @var string
     */
    private const ESCAPE_SEQUENCE_PATTERN = '/\\\\(\\\\|[0-3][0-7]{2})/';

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || \is_string($item);
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_string($item));

        return '"\\\\x'.\bin2hex($item).'"';
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        $result = parent::transformArrayItemForPHP($item);

        if ($result === null) {
            return null;
        }

        // The escape format doubles every backslash, so a value opening with a single one can only be hex
        if (!\str_starts_with($result, '\\x')) {
            return $this->decodeEscapeFormat($result);
        }

        $decoded = @\hex2bin(\substr($result, 2));

        if ($decoded === false) {
            throw InvalidBytesArrayItemForPHPException::forInvalidFormat($result);
        }

        return $decoded;
    }

    /**
     * PostgreSQL writes bytea in the escape format when bytea_output is 'escape': a backslash is doubled,
     * a byte outside printable ASCII is a backslash and three octal digits, and every other byte stands for itself.
     */
    private function decodeEscapeFormat(string $value): string
    {
        if (\preg_match(self::ESCAPE_FORMAT_PATTERN, $value) !== 1) {
            throw InvalidBytesArrayItemForPHPException::forInvalidFormat($value);
        }

        return (string) \preg_replace_callback(
            self::ESCAPE_SEQUENCE_PATTERN,
            static fn (array $matches): string => $matches[1] === '\\' ? '\\' : \pack('C', \octdec($matches[1])),
            $value
        );
    }

    protected function throwInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidBytesArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidBytesArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidBytesArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
