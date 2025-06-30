<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL INT8RANGE (64-bit integer range).
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Int8Range extends BaseIntegerRange
{
    protected static function parseValue(string $value): int
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid integer value: %s', $value)
            );
        }

        $intValue = (int) $value;
        if ((string) $intValue !== $value) {
            throw new \InvalidArgumentException(
                \sprintf('Value %s is not a valid integer', $value)
            );
        }

        return $intValue;
    }
}
