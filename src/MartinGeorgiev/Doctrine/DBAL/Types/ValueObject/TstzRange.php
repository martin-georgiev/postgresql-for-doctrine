<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL TSTZRANGE (timestamp with timezone).
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class TstzRange extends BaseTimestampRange
{
    protected function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('Value must be a DateTimeInterface');
        }

        return $value->format('Y-m-d H:i:s.uP');
    }

    protected static function parseValue(string $value): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid timestamp value: %s. Error: %s', $value, $exception->getMessage()),
                0,
                $exception
            );
        }
    }
}
