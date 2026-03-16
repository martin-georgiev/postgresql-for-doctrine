<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyForPHPException;

/**
 * Implementation of PostgreSQL MONEY data type.
 *
 * Maps to PHP string to avoid locale-dependent parsing issues. PostgreSQL
 * formats money values according to the lc_monetary locale setting, producing
 * locale-specific representations such as "$1,234.56" or "1.234,56 €".
 *
 * @see https://www.postgresql.org/docs/current/datatype-money.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Money extends BaseType
{
    protected const TYPE_NAME = Type::MONEY;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'MONEY';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidMoneyForDatabaseException::forInvalidType($value);
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidMoneyForPHPException::forInvalidType($value);
        }

        return $value;
    }
}
