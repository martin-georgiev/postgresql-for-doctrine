<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;

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
    protected const TYPE_NAME = Type::INTEGER_ARRAY;

    protected function getMinValue(): int
    {
        return -2147483648;
    }

    protected function getMaxValue(): int
    {
        return 2147483647;
    }
}
