<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

/**
 * Casting a float to string is bound by the `precision` ini setting (14 by default), which drops digits the float holds.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class PreciseFloatFormatter
{
    /**
     * Keeps the short form whenever it reads back as the same float, and falls back to the 17-digit form, which always does.
     */
    public static function format(float $value): string
    {
        $shortForm = (string) $value;
        if ((float) $shortForm === $value) {
            return $shortForm;
        }

        return \sprintf('%.17H', $value);
    }
}
