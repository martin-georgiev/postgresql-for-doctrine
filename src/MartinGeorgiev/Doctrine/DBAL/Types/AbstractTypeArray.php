<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Implementation of Postgres' abstract data type
 */
class AbstractTypeArray extends AbstractType
{
    /**
     * Converts a value from its PHP representation to its database representation of the type.
     *
     * @param array $phpArray The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string|null The database representation of the value.
     * 
     * @throws DBALException
     */ 
    public function convertToDatabaseValue($phpArray, AbstractPlatform $platform)
    {
        if (is_null($phpArray)) {
            return null;
        }
        if (!is_array($phpArray)) {
            $exceptionMessage = 'Given value content are not from type "array". Instead it is "%s".';
            throw new DBALException(sprintf($exceptionMessage, gettype($phpArray)));
        }
        foreach ($phpArray as $item) {
            if (!$this->isValidArrayItemForDatabase($item)) {
                $exceptionMessage = 'One or more of items given doesn\'t look like valid.';
                throw new DBALException(sprintf($exceptionMessage));
            }
        }
        return '{'.join(',', $phpArray).'}';
    }
    
    /**
     * Tests if given PHP array item is from compatibale type for the database
     * @param mixed $item
     * @return boolean
     */
    protected function isValidArrayItemForDatabase($item)
    {
        return true;
    }
    
    /**
     * Converts a value from its database representation to its PHP representation of this type.
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
     * Transforms whole postgres array to PHP array
     * @param string $postgresArray
     * @return array
     */
    protected function transformPostgresArrayToPHPArray($postgresArray) {
        $trimmedPostgresArray = mb_substr($postgresArray, 1, -1);
        if ($trimmedPostgresArray === '') {
            return [];
        }
        $phpArray = explode(',', $trimmedPostgresArray);
        return $phpArray;
    }
    
    /**
     * Transforms postgres array item to a PHP compatibale array item
     * @param mixed $item
     * @return mixed
     */
    protected function transformArrayItemForPHP($item)
    {
        return $item;
    }
}
