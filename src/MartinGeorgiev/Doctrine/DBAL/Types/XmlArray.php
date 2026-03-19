<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\XmlValidationTrait;

/**
 * Implementation of PostgreSQL XML[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-xml.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class XmlArray extends BaseStringArray
{
    use XmlValidationTrait;

    protected const TYPE_NAME = Type::XML_ARRAY;

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

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        $result = parent::transformArrayItemForPHP($item);

        if ($result !== null && !$this->isValidXml($result)) {
            throw InvalidXmlArrayItemForPHPException::forInvalidFormat($item);
        }

        return $result;
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidXmlArrayItemForPHPException
    {
        return InvalidXmlArrayItemForPHPException::forInvalidType($item);
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
