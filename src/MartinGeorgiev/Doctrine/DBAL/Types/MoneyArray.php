<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\MoneyValidationTrait;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL MONEY[] data type.
 *
 * Maps array items to PHP strings to avoid locale-dependent parsing issues.
 * PostgreSQL formats money values according to the lc_monetary locale setting.
 *
 * @see https://www.postgresql.org/docs/18/datatype-money.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class MoneyArray extends BaseArray
{
    use MoneyValidationTrait;

    protected const TYPE_NAME = Type::MONEY_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_string($item));
        $escaped = \str_replace(['\\', '"'], ['\\\\', '\\"'], $item);

        return '"'.$escaped.'"';
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return $this->isValidMoneyValue($item);
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidMoneyArrayItemForPHPException::forInvalidType($item);
        }

        return $item;
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidMoneyArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidMoneyArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
