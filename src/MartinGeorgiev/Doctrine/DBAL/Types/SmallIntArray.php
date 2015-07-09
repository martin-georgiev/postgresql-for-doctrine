<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of Postgres' smallint[] data type
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
        return ''.(int)$item === ''.$item;
    }
    
    /**
     * {@inheritDoc}
     */
    public function transformArrayItemForPHP($item)
    {
        return (int)$item;
    }
}
