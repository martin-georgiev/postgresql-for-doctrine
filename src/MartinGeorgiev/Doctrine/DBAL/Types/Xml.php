<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidXmlForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\XmlValidationTrait;

/**
 * Implementation of PostgreSQL XML data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-xml.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Xml extends BaseType
{
    use XmlValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::XML;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidXmlForDatabaseException::forInvalidType($value);
        }

        if (!$this->isValidXmlValue($value)) {
            throw InvalidXmlForDatabaseException::forInvalidFormat($value);
        }

        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidXmlForPHPException::forInvalidType($value);
        }

        if (!$this->isValidXmlValue($value)) {
            throw InvalidXmlForPHPException::forInvalidFormat($value);
        }

        return $value;
    }

    /**
     * Under the default xmloption = content, PostgreSQL stores the empty string as an xml value of its own, not as NULL.
     */
    private function isValidXmlValue(string $value): bool
    {
        return $value === '' || $this->isValidXml($value);
    }
}
