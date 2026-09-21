<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for ULID values.
 *
 * @since 4.8.1
 */
trait UlidValidationTrait
{
    /**
     * @var string
     */
    private const ULID_REGEX = '/^[0-7][0-9A-HJKMNP-TV-Z]{25}\z/i';

    protected function isValidUlid(string $value): bool
    {
        return (bool) \preg_match(self::ULID_REGEX, $value);
    }
}
