<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for lquery path-matching patterns.
 *
 * @since 4.8
 */
trait LqueryValidationTrait
{
    /**
     * A single lquery item: either a star (optionally quantified) or a group of
     * alternative labels with trailing modifiers, optionally negated and quantified.
     *
     * @var string
     */
    private const LQUERY_ITEM = '(?:\*|!?[\p{L}\p{N}_-]+[@*%]*(?:\|[\p{L}\p{N}_-]+[@*%]*)*)(?:\{(?:\d+|\d*,\d*)\})?';

    /**
     * @var string
     */
    // Anchored with \z rather than $, which would also match before a trailing newline.
    private const LQUERY_PATTERN = '/^'.self::LQUERY_ITEM.'(?:\.'.self::LQUERY_ITEM.')*\z/u';

    /**
     * Deliberately permissive: it accepts every pattern PostgreSQL accepts and rejects the
     * structurally broken ones, but it does not enforce semantic constraints such as
     * {n,m} requiring n <= m. PostgreSQL stays the authority on those.
     */
    protected function isValidLquery(string $value): bool
    {
        return \preg_match(self::LQUERY_PATTERN, $value) === 1;
    }
}
