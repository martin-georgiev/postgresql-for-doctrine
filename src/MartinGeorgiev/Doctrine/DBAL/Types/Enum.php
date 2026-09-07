<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumDefinitionException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForPHPException;

/**
 * Abstract base for mapping PostgreSQL native enum types to PHP 8.1+ backed enums.
 *
 * Extend this class, define TYPE_NAME matching your PostgreSQL enum type name exactly,
 * and implement getEnumClass() returning the fully-qualified name of your BackedEnum class.
 *
 * Example:
 *   CREATE TYPE status AS ENUM ('active', 'inactive')
 *
 *   enum Status: string { case ACTIVE = 'active'; case INACTIVE = 'inactive'; }
 *
 *   final class StatusType extends Enum {
 *       protected const TYPE_NAME = 'status';
 *       protected function getEnumClass(): string { return Status::class; }
 *   }
 *
 * @see https://www.postgresql.org/docs/18/datatype-enum.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class Enum extends BaseType
{
    /**
     * @return class-string<\BackedEnum>
     */
    abstract protected function getEnumClass(): string;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $this->getName();
    }

    /**
     * Builds the `CREATE TYPE ... AS ENUM (...)` statement for the mapped PHP enum.
     *
     * PostgreSQL cannot add, rename or remove enum labels inside a transaction, so this
     * library does not hook the statement into Doctrine's schema tool. Run it from a
     * migration instead, where the transactional boundaries are yours to control.
     *
     * @throws InvalidEnumDefinitionException
     *
     * @since 4.8
     */
    public function getCreateTypeSQL(): string
    {
        $labels = [];
        foreach ($this->getEnumClass()::cases() as $backedEnum) {
            if (!\is_string($backedEnum->value)) {
                throw InvalidEnumDefinitionException::forNonStringCaseValue($backedEnum->value);
            }

            $labels[] = \sprintf("'%s'", \str_replace("'", "''", $backedEnum->value));
        }

        return \sprintf('CREATE TYPE %s AS ENUM (%s)', $this->quoteTypeName(), \implode(', ', $labels));
    }

    /**
     * @throws InvalidEnumDefinitionException
     *
     * @since 4.8
     */
    public function getDropTypeSQL(): string
    {
        return \sprintf('DROP TYPE %s', $this->quoteTypeName());
    }

    /**
     * @throws InvalidEnumDefinitionException
     */
    private function quoteTypeName(): string
    {
        $typeName = $this->getName();

        $segments = \explode('.', $typeName);
        if (\count($segments) > 2) {
            throw InvalidEnumDefinitionException::forInvalidTypeName($typeName);
        }

        $quoted = [];
        foreach ($segments as $segment) {
            if (\preg_match('/^[A-Za-z_][A-Za-z0-9_$]*$/', $segment) !== 1) {
                throw InvalidEnumDefinitionException::forInvalidTypeName($typeName);
            }

            $quoted[] = \sprintf('"%s"', $segment);
        }

        return \implode('.', $quoted);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof \BackedEnum) {
            throw InvalidEnumForDatabaseException::forInvalidType($value);
        }

        $enumClass = $this->getEnumClass();
        if (!$value instanceof $enumClass) {
            throw InvalidEnumForDatabaseException::forWrongEnumClass($value, $enumClass);
        }

        return (string) $value->value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\BackedEnum
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidEnumForPHPException::forInvalidType($value);
        }

        $enumClass = $this->getEnumClass();
        if (!\is_subclass_of($enumClass, \BackedEnum::class)) {
            throw InvalidEnumForPHPException::forNonBackedEnum($enumClass);
        }

        $result = $enumClass::tryFrom($value);
        if ($result === null) {
            throw InvalidEnumForPHPException::forUnknownValue($value, $enumClass);
        }

        return $result;
    }
}
