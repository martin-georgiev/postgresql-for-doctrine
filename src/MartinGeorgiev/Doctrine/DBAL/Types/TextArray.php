<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Utils\DataStructure;

/**
 * Implementation of PostgreSql TEXT[] data type
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.6
 * @author Martin Georgiev <martin.georgiev@gmail.com>
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
     * @param array|object|null $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return string|null The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return $this->transformToPostgresTextArray($value);
    }

    /**
     * @param array $phpTextArray
     * @return string
     */
    protected function transformToPostgresTextArray($phpTextArray)
    {
        if (!is_array($phpTextArray)) {
            throw new \InvalidArgumentException(sprintf('Value %s is not an array', var_export($phpTextArray, true)));
        }
        if (empty($phpTextArray)) {
            return '{}';
        }

        return DataStructure::transformPHPArrayToPostgresTextArray($phpTextArray);
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param string|null $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return array|null The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return $this->transformFromPostgresTextArray($value);
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

        return DataStructure::transformPostgresTextArrayToPHPArray($postgresValue);
    }
}
