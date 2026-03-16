<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForPHPException;

/**
 * Implementation of PostgreSQL XML data type.
 *
 * Maps PostgreSQL xml to PHP string. XML well-formedness is enforced by
 * PostgreSQL on write; this type only handles the string conversion.
 *
 * @see https://www.postgresql.org/docs/current/datatype-xml.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Xml extends BaseType
{
    protected const TYPE_NAME = Type::XML;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'XML';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidXmlForPHPException::forInvalidType($value);
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidXmlForDatabaseException::forInvalidType($value);
        }

        return $value;
    }
}
