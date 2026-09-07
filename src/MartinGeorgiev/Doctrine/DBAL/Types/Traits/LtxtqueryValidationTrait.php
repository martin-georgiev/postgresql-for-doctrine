<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for ltxtquery full-text label queries.
 *
 * @since 4.8
 */
trait LtxtqueryValidationTrait
{
    /**
     * Recursive grammar: a word with trailing modifiers, `!` negation, `&`/`|` operators
     * and parenthesised sub-expressions. Only the space character separates tokens —
     * PostgreSQL rejects tabs and newlines inside an ltxtquery.
     *
     * @var string
     */
    private const LTXTQUERY_PATTERN = '/^(?(DEFINE)'
        .'(?<word>[\p{L}\p{N}_-]+[@*%]*)'
        .'(?<term>(?:! *)*(?:(?&word)|\( *(?&expr) *\)))'
        .'(?<expr>(?&term)(?: *[&|] *(?&term))*)'
        .') *(?&expr) *$/u';

    protected function isValidLtxtquery(string $value): bool
    {
        return \preg_match(self::LTXTQUERY_PATTERN, $value) === 1;
    }
}
