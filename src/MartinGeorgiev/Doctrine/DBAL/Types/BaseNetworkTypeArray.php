<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * Base class for network-related PostgreSQL array types (INET[], CIDR[], MACADDR[]).
 *
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseNetworkTypeArray extends BaseArray
{
    /**
     * Always quote network address values when transforming to PostgreSQL format.
     */
    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!\is_string($item)) {
            throw new ConversionException(\sprintf("Value %s can't be resolved to valid network array item", \var_export($item, true)));
        }

        return '"'.$item.'"';
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return $this->isValidNetworkAddress($item);
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        if ($item === null || $item === 'NULL') {
            return null;
        }

        if (!\is_string($item)) {
            $this->throwInvalidTypeException($item);
        }

        // Remove surrounding quotes if present
        $unquotedItem = \trim($item, '"');

        if (!$this->isValidNetworkAddress($unquotedItem)) {
            $this->throwInvalidFormatException($item);
        }

        return $unquotedItem;
    }

    /**
     * Validate if the given string is a valid network address for this type.
     */
    abstract protected function isValidNetworkAddress(string $value): bool;

    /**
     * Get the exception to throw when the format is invalid.
     */
    abstract protected function throwInvalidFormatException(mixed $value): \Exception;
}
