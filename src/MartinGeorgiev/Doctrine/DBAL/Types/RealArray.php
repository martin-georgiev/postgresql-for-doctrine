<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Utils\PostgresFloat;

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

    /**
     * The midpoint between the largest real and 2^128. PostgreSQL rounds a smaller magnitude down to the largest real and
     * rejects this one and anything larger.
     *
     * @var string
     */
    private const OVERFLOW_MIDPOINT = '340282356779733661637539395458142568448';

    /**
     * Half the smallest subnormal real, 2^-150. PostgreSQL rounds a non-zero magnitude up to the smallest real only
     * above it, and rejects this one and anything smaller.
     *
     * @var string
     */
    private const UNDERFLOW_MIDPOINT = '7.00649232162408535461864791644958065640130970938257885878534141944895541342930300743319094181060791015625E-46';

    protected function getMinValue(): string
    {
        return '-3.4028235677973366E+38';
    }

    protected function getMaxValue(): string
    {
        return '3.4028235677973366E+38';
    }

    /**
     * PostgreSQL rounds the text straight to real, while PHP rounds it to a double first. Near the two midpoints that
     * double can land on the other side, so the range is decided on the text as written.
     */
    protected function isBelowMinValue(string $value, float $floatValue): bool
    {
        return $floatValue < 0 && PostgresFloat::compareMagnitudes($value, self::OVERFLOW_MIDPOINT) >= 0;
    }

    protected function isAboveMaxValue(string $value, float $floatValue): bool
    {
        return $floatValue > 0 && PostgresFloat::compareMagnitudes($value, self::OVERFLOW_MIDPOINT) >= 0;
    }

    protected function roundsToZero(string $value, float $floatValue): bool
    {
        return PostgresFloat::compareMagnitudes($value, '0') !== 0
            && PostgresFloat::compareMagnitudes($value, self::UNDERFLOW_MIDPOINT) <= 0;
    }
}
