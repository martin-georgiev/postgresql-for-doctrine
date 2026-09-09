<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\LtxtqueryValidationTrait;

/**
 * Implementation of PostgreSQL ltxtquery data type.
 *
 * Full-text style query over the labels of an ltree value, combining words with `&`, `|`, `!`
 * and parentheses, where each word may carry the `@`, `*` and `%` modifiers.
 * Requires the ltree extension.
 *
 * @see https://www.postgresql.org/docs/18/ltree.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Ltxtquery extends BaseType
{
    use LtxtqueryValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::LTXTQUERY;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidLtxtqueryForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidLtxtquery($value)) {
            throw InvalidLtxtqueryForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }

    /**
     * Format is deliberately not re-validated here. PostgreSQL already accepted and normalized
     * the stored query, so a value coming back from the database is authoritative; applying the
     * approximating pattern of the validation trait on read could make stored rows unreadable.
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidLtxtqueryForPHPException::forInvalidType($value);
        }

        return $value;
    }
}
