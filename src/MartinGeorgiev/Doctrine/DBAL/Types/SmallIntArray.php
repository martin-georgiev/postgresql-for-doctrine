<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSql SMALLINT[] data type
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
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
        return (is_int($item) || is_string($item)) && preg_match('/^-?[0-9]+$/', (string)$item);
    }
    
    /**
     * {@inheritDoc}
     */
    public function transformArrayItemForPHP($item)
    {
        return (int)$item;
    }
}
