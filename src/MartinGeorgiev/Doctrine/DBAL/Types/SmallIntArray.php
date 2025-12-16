<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;

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
    protected const TYPE_NAME = Type::SMALLINT_ARRAY;

    protected function getMinValue(): int
    {
        return -32768;
    }

    protected function getMaxValue(): int
    {
        return 32767;
    }
}
