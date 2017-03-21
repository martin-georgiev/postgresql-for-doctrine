<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * Implementation of PostgreSql JSONB data type
 * @see https://www.postgresql.org/docs/9.4/static/datatype-json.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Jsonb extends AbstractType
{
    use JsonTransformer;
    
    /**
     * @var string
     */
    const TYPE_NAME = 'jsonb';
    
    /**
     * Converts a value from its PHP representation to its database representation of the type.
     *
     * @param mixed $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string The database representation of the value.
     *
     * @throws ConversionException When given value cannot be encoded
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }
        $encodedValue = $this->transformToPostgresJson($value);
        if ($encodedValue === false) {
            throw new ConversionException('Given value content cannot be encoded to valid json.');
        }
        return $encodedValue;
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
        $json = $this->transformFromPostgresJson($value);
        return $json;
    }
}
