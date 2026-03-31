<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Base class of PostgreSQL geometric array data types.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseGeometricArray extends BaseArray
{
    /**
     * @return class-string<\Stringable>
     */
    abstract protected function getValueObjectClass(): string;

    abstract protected function createValueObjectFromString(string $value): \Stringable;

    abstract protected function throwTypedInvalidArrayTypeException(mixed $value): never;

    abstract protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never;

    abstract protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        $class = $this->getValueObjectClass();
        if (!$item instanceof $class) {
            $this->throwTypedInvalidItemExceptionForDatabase($item);
        }

        return '"'.$item.'"';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        if (!\str_starts_with($postgresArray, '{"') || !\str_ends_with($postgresArray, '"}')) {
            return [];
        }

        $trimmedPostgresArray = \mb_substr($postgresArray, 2, -2);
        if ($trimmedPostgresArray === '') {
            return [];
        }

        return \explode('","', $trimmedPostgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?\Stringable
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            $this->throwTypedInvalidFormatExceptionForPHP($item);
        }

        try {
            return $this->createValueObjectFromString($item);
        } catch (\InvalidArgumentException) {
            $this->throwTypedInvalidFormatExceptionForPHP($item);
        }
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item instanceof ($this->getValueObjectClass());
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        $this->throwTypedInvalidArrayTypeException($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        $this->throwTypedInvalidItemExceptionForDatabase($item);
    }
}
