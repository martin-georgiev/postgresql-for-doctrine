<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;

/**
 * Implementation of PostgreSQL DOUBLE PRECISION[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-numeric.html
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DoublePrecisionArray extends BaseFloatArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::DOUBLE_PRECISION_ARRAY;

    protected function getMinValue(): string
    {
        return '-1.7976931348623157E+308';
    }

    protected function getMaxValue(): string
    {
        return '1.7976931348623157E+308';
    }

    /**
     * A PHP float is a double, so a value that rounds to zero in double precision already parses as zero.
     */
    protected function getLargestMagnitudeRoundingToZero(): string
    {
        return '0';
    }
}
