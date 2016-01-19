<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSql smallint[] data type
 */
class SmallIntArray extends AbstractTypeArray
{
    /**
     * @var string
     */
    const TYPE_NAME = 'smallint[]';
    
    /**
     * {@inheritDoc}
     */
    public function isValidArrayItemForDatabase($item)
    {
        return (is_integer($item) || is_string($item)) && preg_match('/^-?[0-9]+$/', (string)$item);
    }
    
    /**
     * {@inheritDoc}
     */
    public function transformArrayItemForPHP($item)
    {
        return (int)$item;
    }
}
