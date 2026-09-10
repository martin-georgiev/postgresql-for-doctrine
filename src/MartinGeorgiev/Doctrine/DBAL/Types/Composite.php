<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeForPHPException;
use MartinGeorgiev\Utils\Exception\InvalidRecordFormatException;
use MartinGeorgiev\Utils\PHPArrayToPostgresRecordTransformer;
use MartinGeorgiev\Utils\PostgresRecordToPHPArrayTransformer;

/**
 * Abstract base for mapping PostgreSQL composite (row) types to PHP arrays keyed by field name.
 *
 * Extend this class, define TYPE_NAME matching your PostgreSQL composite type name exactly,
 * and implement getFieldTypes() returning the field names mapped to Doctrine type names,
 * in the exact order the fields were declared in CREATE TYPE.
 *
 * Composite record literals are positional - the field names exist only on the PHP side, so a
 * getFieldTypes() ordering that disagrees with the PostgreSQL declaration silently mixes up columns.
 *
 * Example:
 *   CREATE TYPE inventory_item AS (name text, supplier_id integer, price numeric)
 *
 *   final class InventoryItemType extends Composite {
 *       protected const TYPE_NAME = 'inventory_item';
 *       protected function getFieldTypes(): array {
 *           return ['name' => Types::TEXT, 'supplier_id' => Types::INTEGER, 'price' => Types::DECIMAL];
 *       }
 *   }
 *
 * @see https://www.postgresql.org/docs/18/rowtypes.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class Composite extends BaseType
{
    /**
     * Field name mapped to the Doctrine type used to convert it, in PostgreSQL declaration order.
     *
     * @return array<string, string>
     */
    abstract protected function getFieldTypes(): array;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $this->getName();
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidCompositeForDatabaseException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw InvalidCompositeForDatabaseException::forInvalidType($value);
        }

        $fieldTypes = $this->getFieldTypes();
        $this->assertFieldNamesMatchDeclaration($value, $fieldTypes);

        $fields = [];
        foreach ($fieldTypes as $fieldName => $fieldType) {
            $fields[] = $this->convertFieldForDatabase($value[$fieldName], $fieldType, $fieldName, $platform);
        }

        return PHPArrayToPostgresRecordTransformer::transformPHPArrayToPostgresRecord($fields);
    }

    /**
     * @param mixed $value
     *
     * @return array<string, mixed>|null
     *
     * @throws InvalidCompositeForPHPException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidCompositeForPHPException::forInvalidType($value);
        }

        $fieldTypes = $this->getFieldTypes();

        try {
            $rawFields = PostgresRecordToPHPArrayTransformer::transformPostgresRecordToPHPArray($value);
        } catch (InvalidRecordFormatException) {
            throw InvalidCompositeForPHPException::forInvalidFormat($value);
        }

        if (\count($rawFields) !== \count($fieldTypes)) {
            throw InvalidCompositeForPHPException::forUnexpectedFieldCount(\count($fieldTypes), \count($rawFields), $value);
        }

        $result = [];
        $position = 0;
        foreach ($fieldTypes as $fieldName => $fieldType) {
            $result[$fieldName] = DoctrineType::getType($fieldType)->convertToPHPValue($rawFields[$position], $platform);
            $position++;
        }

        return $result;
    }

    /**
     * @param array<array-key, mixed> $value
     * @param array<string, string> $fieldTypes
     *
     * @throws InvalidCompositeForDatabaseException
     */
    private function assertFieldNamesMatchDeclaration(array $value, array $fieldTypes): void
    {
        $missing = \array_diff(\array_keys($fieldTypes), \array_keys($value));
        if ($missing !== []) {
            throw InvalidCompositeForDatabaseException::forMissingFields(\implode(', ', $missing));
        }

        $unknown = \array_diff(\array_keys($value), \array_keys($fieldTypes));
        if ($unknown !== []) {
            throw InvalidCompositeForDatabaseException::forUnknownFields(\implode(', ', \array_map(\strval(...), $unknown)));
        }
    }

    /**
     * @throws InvalidCompositeForDatabaseException
     */
    private function convertFieldForDatabase(mixed $fieldValue, string $fieldType, string $fieldName, AbstractPlatform $platform): ?string
    {
        $converted = DoctrineType::getType($fieldType)->convertToDatabaseValue($fieldValue, $platform);

        return match (true) {
            $converted === null => null,
            \is_string($converted) => $converted,
            // PostgreSQL accepts 1/0 for boolean, which is what its platform returns for a bool field
            \is_bool($converted) => $converted ? '1' : '0',
            \is_int($converted), \is_float($converted) => (string) $converted,
            default => throw InvalidCompositeForDatabaseException::forUnsupportedFieldValue($fieldName, $converted),
        };
    }
}
