<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;

/**
 * Implementation of PostgreSQL BOX[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class BoxArray extends BaseGeometricArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BOX_ARRAY;

    protected function getValueObjectClass(): string
    {
        return BoxValueObject::class;
    }

    protected function createValueObjectFromString(string $value): BoxValueObject
    {
        return BoxValueObject::fromString($value);
    }

    public function convertToDatabaseValue($phpArray, AbstractPlatform $platform): ?string
    {
        if ($phpArray === null) {
            return null;
        }

        if (!\is_array($phpArray)) {
            $this->throwInvalidTypeException($phpArray);
        }

        $transformedItems = [];
        foreach ($phpArray as $item) {
            if (!$this->isValidArrayItemForDatabase($item)) {
                $this->throwInvalidItemException($item);
            }

            \assert($item instanceof BoxValueObject);
            $transformedItems[] = $item->__toString();
        }

        return '{'.\implode(';', $transformedItems).'}';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        $trimmed = \mb_substr($postgresArray, 1, -1);
        if ($trimmed === '') {
            return [];
        }

        return \explode(';', $trimmed);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidBoxArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidBoxArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidBoxArrayItemForDatabaseException::forInvalidType($item);
    }
}
