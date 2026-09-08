<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeForPHPException;
use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Abstract base for mapping arrays of PostgreSQL composite (row) types to PHP arrays of field-keyed arrays.
 *
 * Extend this class, define TYPE_NAME as the PostgreSQL composite type name followed by [], and implement
 * getCompositeClass() returning the Composite subclass each item maps to.
 *
 * Example:
 *   CREATE TYPE inventory_item AS (name text, supplier_id integer, price numeric);
 *   CREATE TABLE orders (id serial PRIMARY KEY, items inventory_item[]);
 *
 *   final class InventoryItemType extends Composite {
 *       protected const TYPE_NAME = 'inventory_item';
 *       protected function getFieldTypes(): array {
 *           return ['name' => Types::TEXT, 'supplier_id' => Types::INTEGER, 'price' => Types::FLOAT];
 *       }
 *   }
 *
 *   final class InventoryItemArrayType extends CompositeArray {
 *       protected const TYPE_NAME = 'inventory_item[]';
 *       protected function getCompositeClass(): string { return InventoryItemType::class; }
 *   }
 *
 *   Type::addType('inventory_item', InventoryItemType::class);
 *   Type::addType('inventory_item[]', InventoryItemArrayType::class);
 *
 * @see https://www.postgresql.org/docs/18/rowtypes.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class CompositeArray extends BaseArray
{
    /**
     * BaseArray's per-item hooks are not given the platform, but converting an item means handing it to the scalar
     * composite type, which needs one. It is captured on entry and read only within that same call.
     */
    private ?AbstractPlatform $conversionPlatform = null;

    private ?Composite $compositeType = null;

    /**
     * The scalar composite type each item is converted by.
     *
     * @return class-string<Composite>
     */
    abstract protected function getCompositeClass(): string;

    /**
     * The name is user-defined, so it has no platform mapping to look up the way built-in types do.
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $this->getName();
    }

    /**
     * @param array|null $phpArray
     */
    public function convertToDatabaseValue($phpArray, AbstractPlatform $platform): ?string
    {
        $this->conversionPlatform = $platform;

        return parent::convertToDatabaseValue($phpArray, $platform);
    }

    /**
     * @param string|null $postgresArray
     */
    public function convertToPHPValue($postgresArray, AbstractPlatform $platform): ?array
    {
        $this->conversionPlatform = $platform;

        return parent::convertToPHPValue($postgresArray, $platform);
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || \is_array($item);
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!\is_array($item)) {
            $this->throwInvalidItemException($item);
        }

        $literal = $this->getCompositeType()->convertToDatabaseValue($item, $this->getPlatform());

        return $this->quoteAndEscapeArrayItem((string) $literal);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function transformArrayItemForPHP(mixed $item): ?array
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidCompositeArrayItemForPHPException::forInvalidFormat($item);
        }

        try {
            return $this->getCompositeType()->convertToPHPValue($item, $this->getPlatform());
        } catch (InvalidCompositeForPHPException) {
            throw InvalidCompositeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    /**
     * A bare NULL element must become PHP null rather than the string "NULL", so string types are not preserved.
     * Record literals arrive quoted and survive either way.
     */
    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        try {
            return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
        } catch (InvalidArrayFormatException) {
            throw InvalidCompositeArrayItemForPHPException::forInvalidFormat($postgresArray);
        }
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidCompositeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidCompositeArrayItemForDatabaseException::forInvalidType($item);
    }

    private function getCompositeType(): Composite
    {
        $compositeClass = $this->getCompositeClass();

        return $this->compositeType ??= new $compositeClass();
    }

    private function getPlatform(): AbstractPlatform
    {
        \assert($this->conversionPlatform instanceof AbstractPlatform);

        return $this->conversionPlatform;
    }
}
