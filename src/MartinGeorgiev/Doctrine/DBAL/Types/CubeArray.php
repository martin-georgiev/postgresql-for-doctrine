<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube as CubeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCubeException;

/**
 * Implementation of PostgreSQL CUBE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/cube.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class CubeArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::CUBE_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item instanceof CubeValueObject;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if (!$item instanceof CubeValueObject) {
            $this->throwInvalidItemException($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    public function transformArrayItemForPHP(mixed $item): ?CubeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidCubeArrayItemForPHPException::forInvalidFormat($item);
        }

        try {
            return CubeValueObject::fromString($item);
        } catch (InvalidCubeException) {
            throw InvalidCubeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function throwInvalidArrayFormatException(string $postgresArray): never
    {
        throw InvalidCubeArrayItemForPHPException::forInvalidFormat($postgresArray);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidCubeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidCubeArrayItemForDatabaseException::forInvalidType($item);
    }
}
