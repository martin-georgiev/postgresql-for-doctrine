<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Multirange;

/**
 * Base class of PostgreSQL multirange array data types.
 *
 * @template M of Multirange
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.8.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseMultirangeArray extends BaseArray
{
    /**
     * @return class-string<M>
     */
    abstract protected function getValueObjectClass(): string;

    /**
     * @return M
     */
    abstract protected function createValueObjectFromString(string $value): Multirange;

    abstract protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException;

    abstract protected function throwTypedInvalidArrayTypeException(mixed $value): never;

    abstract protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never;

    abstract protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        $class = $this->getValueObjectClass();
        if (!$item instanceof $class) {
            $this->throwTypedInvalidItemExceptionForDatabase($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof ($this->getValueObjectClass());
    }

    /**
     * @return M|null
     */
    public function transformArrayItemForPHP(mixed $item): ?Multirange
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw $this->createInvalidTypeExceptionForPHP($item);
        }

        try {
            return $this->createValueObjectFromString($item);
        } catch (\InvalidArgumentException) {
            $this->throwTypedInvalidFormatExceptionForPHP($item);
        }
    }

    protected function throwInvalidArrayFormatException(string $postgresArray): never
    {
        $this->throwTypedInvalidFormatExceptionForPHP($postgresArray);
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
