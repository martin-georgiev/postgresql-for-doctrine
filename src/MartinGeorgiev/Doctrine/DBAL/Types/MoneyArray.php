<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\MoneyValidationTrait;

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
class MoneyArray extends BaseStringArray
{
    use MoneyValidationTrait;

    protected const TYPE_NAME = Type::MONEY_ARRAY;

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

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidMoneyArrayItemForPHPException
    {
        return InvalidMoneyArrayItemForPHPException::forInvalidType($item);
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
