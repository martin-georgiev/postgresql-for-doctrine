<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * Implementation of PostgreSql text[] data type
 */
class TextArray extends AbstractType
{
    /**
     * @var string
     */
    const TYPE_NAME = 'text[]';

    /**
     * Converts a value from its PHP representation to its database representation of the type.
     *
     * @param mixed $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string The database representation of the value.
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }
        $encodedValue = $this->transformToPostgresTextArray($value);
        if ($encodedValue === false) {
            throw new ConversionException('Given value content cannot be encoded to valid json.');
        }
        return $encodedValue;
    }

    /**
     * @param array $phpTextArray
     * @return string
     */
    protected function transformToPostgresTextArray($phpTextArray)
    {
        if (!is_array($phpTextArray)) {
            return false;
        }
        if (!$phpTextArray) {
            return '{}';
        }
        return '{"' . join('","', $phpTextArray) . '"}';
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param string $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return array The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        $textArray = $this->transformFromPostgresTextArray($value);
        return $textArray;
    }

    /**
     * @param string $postgresValue
     * @return array
     */
    protected function transformFromPostgresTextArray($postgresValue)
    {
        if ($postgresValue === '{}') {
            return [];
        }
        $trimmedPostgresValue = mb_substr($postgresValue, 2, -2);
        return explode('","', $trimmedPostgresValue);
    }
}
