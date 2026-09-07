<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\LqueryValidationTrait;

/**
 * Implementation of PostgreSQL lquery[] data type.
 *
 * @see https://www.postgresql.org/docs/18/ltree.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class LqueryArray extends BaseStringArray
{
    use LqueryValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::LQUERY_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return $this->isValidLquery($item);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidLqueryArrayItemForPHPException
    {
        return InvalidLqueryArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidLqueryArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw \is_string($item)
            ? InvalidLqueryArrayItemForDatabaseException::forInvalidFormat($item)
            : InvalidLqueryArrayItemForDatabaseException::forInvalidType($item);
    }
}
