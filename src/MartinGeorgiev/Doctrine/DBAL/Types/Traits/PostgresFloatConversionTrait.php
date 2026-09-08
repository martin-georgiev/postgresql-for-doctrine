<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait PostgresFloatConversionTrait
{
    /**
     * Casting a float to string is bound by the `precision` ini setting (14 by default). This rewrites the value before
     * it reaches PostgreSQL. Fall back to the 17-digit form, which always round-trips, whenever the short one does not.
     */
    protected static function formatFloat(float $value): string
    {
        if (\is_nan($value)) {
            return 'NaN';
        }

        if (\is_infinite($value)) {
            return $value > 0 ? 'Infinity' : '-Infinity';
        }

        $shortForm = (string) $value;
        if ((float) $shortForm === $value) {
            return $shortForm;
        }

        return \sprintf('%.17H', $value);
    }
}
