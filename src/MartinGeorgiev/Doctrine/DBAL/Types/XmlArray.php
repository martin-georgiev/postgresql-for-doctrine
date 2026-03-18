<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\XmlValidationTrait;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL XML[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-xml.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class XmlArray extends BaseArray
{
    use XmlValidationTrait;

    protected const TYPE_NAME = Type::XML_ARRAY;

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

        return $this->isValidXml($item);
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
            throw InvalidXmlArrayItemForPHPException::forInvalidType($item);
        }

        if (!$this->isValidXml($item)) {
            throw InvalidXmlArrayItemForPHPException::forInvalidFormat($item);
        }

        return $item;
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidXmlArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidXmlArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
