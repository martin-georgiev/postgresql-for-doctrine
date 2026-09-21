<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidRangeException;

/**
 * Represents a PostgreSQL timestamp range without timezone.
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class TsRange extends BaseTimestampRange
{
    protected function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $value);
        }

        return $value->format('Y-m-d H:i:s.u');
    }
}
