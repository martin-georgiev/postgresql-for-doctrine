<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Recognizes the tokens PostgreSQL reads for a date, timestamp or range bound that lies outside any calendar or ordering.
 *
 * The grammar is narrower than the one the numeric types use. E.g. `inf` is a float and numeric spelling that a date rejects, so it is deliberately absent here.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait PostgresInfinityConversionTrait
{
    protected static function normalizeInfinity(string $value): ?string
    {
        return match (\mb_strtolower($value)) {
            'infinity', '+infinity' => 'infinity',
            '-infinity' => '-infinity',
            default => null,
        };
    }
}
