<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCitextArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCitextArrayItemForPHPException;

/**
 * Implementation of PostgreSQL citext[] data type.
 *
 * @see https://www.postgresql.org/docs/18/citext.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class CitextArray extends BaseStringArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::CITEXT_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || \is_string($item);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidCitextArrayItemForPHPException
    {
        return InvalidCitextArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidCitextArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidCitextArrayItemForDatabaseException::forInvalidType($item);
    }
}
