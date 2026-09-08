<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\LqueryValidationTrait;

/**
 * Implementation of PostgreSQL lquery data type.
 *
 * Path-matching pattern for ltree values, supporting `*` wildcards with `{n,m}` quantifiers,
 * `|` alternatives, `!` negation and the `@`, `*` and `%` label modifiers.
 * Requires the ltree extension.
 *
 * @see https://www.postgresql.org/docs/18/ltree.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Lquery extends BaseType
{
    use LqueryValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::LQUERY;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidLqueryForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidLquery($value)) {
            throw InvalidLqueryForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }

    /**
     * Format is deliberately not re-validated here. PostgreSQL already accepted and normalized
     * the stored pattern, so a value coming back from the database is authoritative; applying the
     * approximating pattern of the validation trait on read could make stored rows unreadable.
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidLqueryForPHPException::forInvalidType($value);
        }

        return $value;
    }
}
