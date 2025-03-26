<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSQL DOUBLE PRECISION[] data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-numeric.html
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DoublePrecisionArray extends BaseFloatArray
{
    protected const TYPE_NAME = 'double precision[]';

    protected function getMinValue(): string
    {
        return '-1.7976931348623157E+308';
    }

    protected function getMaxValue(): string
    {
        return '1.7976931348623157E+308';
    }

    protected function getMaxPrecision(): int
    {
        return 15;
    }

    protected function getMinAbsoluteValue(): string
    {
        return '2.2250738585072014E-308';
    }
}
