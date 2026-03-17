<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlArrayItemForPHPException;
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
    protected const TYPE_NAME = Type::XML_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        $escaped = \str_replace(['\\', '"'], ['\\\\', '\\"'], $item); // @phpstan-ignore-line $item validated by isValidArrayItemForDatabase

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

    private function isValidXml(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $previousUseInternalErrors = \libxml_use_internal_errors(true);

        try {
            $domDocument = new \DOMDocument();
            $loaded = $domDocument->loadXML($value);

            return $loaded && \libxml_get_errors() === [];
        } finally {
            \libxml_clear_errors();
            \libxml_use_internal_errors($previousUseInternalErrors);
        }
    }
}
