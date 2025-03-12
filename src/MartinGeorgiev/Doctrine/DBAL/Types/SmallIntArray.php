<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSQL SMALLINT[] data type.
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class SmallIntArray extends BaseIntegerArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = 'smallint[]';

    protected function getMinValue(): string
    {
        return '-32768';
    }

    protected function getMaxValue(): string
    {
        return '32767';
    }
}
