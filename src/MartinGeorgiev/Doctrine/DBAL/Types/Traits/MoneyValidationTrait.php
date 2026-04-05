<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for PostgreSQL MONEY values.
 *
 * @since 4.4
 */
trait MoneyValidationTrait
{
    protected function isValidMoneyValue(string $value): bool
    {
        return \preg_match('/\d/', $value) === 1;
    }
}
