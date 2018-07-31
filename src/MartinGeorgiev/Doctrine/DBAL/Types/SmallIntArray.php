<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSql SMALLINT[] data type
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class SmallIntArray extends AbstractIntegerArray
{
    /**
     * @var string
     */
    const TYPE_NAME = 'smallint[]';

    /**
     * @return string
     */
    protected function getMinValue()
    {
        return '-32768';
    }

    /**
     * @return string
     */
    protected function getMaxValue()
    {
        return '32767';
    }
}
