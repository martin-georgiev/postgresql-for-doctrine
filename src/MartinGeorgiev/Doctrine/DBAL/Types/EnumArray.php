<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumArrayItemForPHPException;
use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Abstract base for mapping arrays of PostgreSQL native enum types to arrays of PHP 8.1+ backed enums.
 *
 * Extend this class, define TYPE_NAME as your PostgreSQL enum type name followed by "[]",
 * and implement getEnumClass() returning the fully-qualified name of your BackedEnum class.
 *
 * Example:
 *   CREATE TYPE status AS ENUM ('active', 'inactive')
 *
 *   enum Status: string { case ACTIVE = 'active'; case INACTIVE = 'inactive'; }
 *
 *   final class StatusArrayType extends EnumArray {
 *       protected const TYPE_NAME = 'status[]';
 *       protected function getEnumClass(): string { return Status::class; }
 *   }
 *
 * @see https://www.postgresql.org/docs/18/datatype-enum.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class EnumArray extends BaseArray
{
    /**
     * @var string
     */
    private const POSTGRES_NULL_ELEMENT = 'NULL';

    /**
     * @return class-string<\BackedEnum>
     */
    abstract protected function getEnumClass(): string;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $this->getName();
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        $enumClass = $this->getEnumClass();

        return $item instanceof $enumClass;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return self::POSTGRES_NULL_ELEMENT;
        }

        if (!$item instanceof \BackedEnum) {
            $this->throwInvalidItemException($item);
        }

        // Labels are quoted unconditionally: an enum label may hold a comma, a space, a double quote,
        // a backslash or be empty, and PostgreSQL only parses those back correctly when quoted.
        return $this->quoteAndEscapeArrayItem((string) $item->value);
    }

    public function transformArrayItemForPHP(mixed $item): ?\BackedEnum
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidEnumArrayItemForPHPException::forInvalidType($item);
        }

        $enumClass = $this->getEnumClass();
        if (!\is_subclass_of($enumClass, \BackedEnum::class)) {
            throw InvalidEnumArrayItemForPHPException::forNonBackedEnum($enumClass);
        }

        $case = $enumClass::tryFrom($item);
        if ($case === null) {
            throw InvalidEnumArrayItemForPHPException::forUnknownValue($item, $enumClass);
        }

        return $case;
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        try {
            // PostgreSQL leaves numeric- and boolean-looking labels (e.g. 42, true) unquoted;
            // without string preservation they would be coerced away from their label form.
            $phpArray = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray, true);
        } catch (InvalidArrayFormatException) {
            throw InvalidEnumArrayItemForPHPException::forInvalidFormat($postgresArray);
        }

        // String preservation also keeps the unquoted NULL element marker as a plain string,
        // so map it back to null here. A label spelled exactly "NULL" is indistinguishable
        // from a NULL element once quoting is lost and is therefore read back as null.
        return \array_map(
            static fn (mixed $item): mixed => $item === self::POSTGRES_NULL_ELEMENT ? null : $item,
            $phpArray
        );
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidEnumArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        if ($item instanceof \BackedEnum) {
            throw InvalidEnumArrayItemForDatabaseException::forWrongEnumClass($item, $this->getEnumClass());
        }

        throw InvalidEnumArrayItemForDatabaseException::forInvalidType($item);
    }
}
