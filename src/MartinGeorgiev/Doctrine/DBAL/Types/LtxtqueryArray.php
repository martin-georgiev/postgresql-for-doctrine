<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\LtxtqueryValidationTrait;

/**
 * Implementation of PostgreSQL ltxtquery[] data type.
 *
 * @see https://www.postgresql.org/docs/18/ltree.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class LtxtqueryArray extends BaseStringArray
{
    use LtxtqueryValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::LTXTQUERY_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return $this->isValidLtxtquery($item);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidLtxtqueryArrayItemForPHPException
    {
        return InvalidLtxtqueryArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidLtxtqueryArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw \is_string($item)
            ? InvalidLtxtqueryArrayItemForDatabaseException::forInvalidFormat($item)
            : InvalidLtxtqueryArrayItemForDatabaseException::forInvalidType($item);
    }
}
