<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

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
    public const TYPE_NAME = 'real[]';

    protected function getMinValue(): string
    {
        return '-3.4028235E+38';
    }

    protected function getMaxValue(): string
    {
        return '3.4028235E+38';
    }

    protected function getMaxPrecision(): int
    {
        return 6;
    }

    protected function getMinAbsoluteValue(): string
    {
        return '1.17549435E-38';
    }
}
