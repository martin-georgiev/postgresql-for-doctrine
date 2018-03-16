<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * Abstract handling of PostgreSql array data types
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class AbstractTypeArray extends AbstractType
{
    /**
     * Converts a value from its PHP representation to its PostgreSql representation of the type.
     *
     * @param array $phpArray The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string|null The database representation of the value.
     *
     * @throws ConversionException When passed argument is not PHP array OR When invalid array items are detected
     */
    public function convertToDatabaseValue($phpArray, AbstractPlatform $platform)
    {
        if (null === $phpArray) {
            return null;
        }

        if (!is_array($phpArray)) {
            $exceptionMessage = 'Given PHP value content type is not PHP array. Instead it is "%s".';

            throw new ConversionException(sprintf($exceptionMessage, gettype($phpArray)));
        }

        foreach ($phpArray as &$item) {
            if (!$this->isValidArrayItemForDatabase($item)) {
                $exceptionMessage = 'One or more of items given doesn\'t look like valid.';

                throw new ConversionException($exceptionMessage);
            }
            $item = $this->transformArrayItemForPostgres($item);
        }

        return '{'.implode(',', $phpArray).'}';
    }

    /**
     * Tests if given PHP array item is from compatible type for PostgreSql
     *
     * @param mixed $item
     *
     * @return bool
     */
    protected function isValidArrayItemForDatabase($item)
    {
        return true;
    }

    /**
     * Transforms PHP array item to a PostgreSql compatible array item
     *
     * @param mixed $item
     *
     * @return mixed
     */
    protected function transformArrayItemForPostgres($item)
    {
        return $item;
    }

    /**
     * Converts a value from its PostgreSql representation to its PHP representation of this type.
     *
     * @param mixed $postgresArray The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return array|null The PHP representation of the value.
     */
    public function convertToPHPValue($postgresArray, AbstractPlatform $platform)
    {
        if ($postgresArray === null) {
            return null;
        }
        $phpArray = $this->transformPostgresArrayToPHPArray($postgresArray);
        foreach ($phpArray as &$item) {
            $item = $this->transformArrayItemForPHP($item);
        }

        return $phpArray;
    }

    /**
     * Transforms whole PostgreSql array to PHP array
     *
     * @param string $postgresArray
     *
     * @return array
     *
     * @throws ConversionException When passed argument is not PHP string
     */
    protected function transformPostgresArrayToPHPArray($postgresArray)
    {
        if (!is_string($postgresArray)) {
            $exceptionMessage = 'Given PostgreSql value content type is not PHP string. Instead it is "%s".';

            throw new ConversionException(sprintf($exceptionMessage, gettype($postgresArray)));
        }
        $trimmedPostgresArray = mb_substr($postgresArray, 1, -1);
        if ($trimmedPostgresArray === '') {
            return [];
        }
        $phpArray = explode(',', $trimmedPostgresArray);

        return $phpArray;
    }

    /**
     * Transforms PostgreSql array item to a PHP compatible array item
     *
     * @param mixed $item
     *
     * @return mixed
     */
    protected function transformArrayItemForPHP($item)
    {
        return $item;
    }
}
