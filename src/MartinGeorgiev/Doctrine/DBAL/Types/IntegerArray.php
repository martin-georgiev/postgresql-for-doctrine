<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSQL INTEGER[] data type.
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class IntegerArray extends BaseIntegerArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = 'integer[]';

    protected function getMinValue(): string
    {
        return '-2147483648';
    }

    protected function getMaxValue(): string
    {
        return '2147483647';
    }
}
