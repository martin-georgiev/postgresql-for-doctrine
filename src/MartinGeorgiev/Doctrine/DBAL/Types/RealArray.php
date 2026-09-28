<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;

/**
 * Implementation of PostgreSQL REAL[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-numeric.html
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class RealArray extends BaseFloatArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::REAL_ARRAY;

    protected function getMinValue(): string
    {
        return '-3.4028235677973366E+38';
    }

    /**
     * The midpoint between the largest real and 2^128. PostgreSQL rounds anything below it down to the largest real
     * and rejects anything above it.
     */
    protected function getMaxValue(): string
    {
        return '3.4028235677973366E+38';
    }

    /**
     * Half the smallest subnormal real, 2^-150: a tie at it rounds to even, which is zero.
     */
    protected function getLargestMagnitudeRoundingToZero(): string
    {
        return '7.006492321624085E-46';
    }
}
