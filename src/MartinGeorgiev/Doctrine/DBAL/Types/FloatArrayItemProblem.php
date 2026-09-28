<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForDatabaseException;

/**
 * Why a float array item cannot be written, kept apart from the exception that reports it.
 *
 * @internal
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
enum FloatArrayItemProblem
{
    public function toException(mixed $item): InvalidFloatArrayItemForDatabaseException
    {
        return match ($this) {
            self::NotANumber => InvalidFloatArrayItemForDatabaseException::isNotANumber($item),
            self::NotAFloatLiteral => InvalidFloatArrayItemForDatabaseException::doesNotMatchRegex($item),
            self::BelowMinValue => InvalidFloatArrayItemForDatabaseException::isBelowMinValue($item),
            self::AboveMaxValue => InvalidFloatArrayItemForDatabaseException::isAboveMaxValue($item),
            self::RoundsToZero => InvalidFloatArrayItemForDatabaseException::absoluteValueIsTooCloseToZero($item),
        };
    }

    case NotANumber;

    case NotAFloatLiteral;

    case BelowMinValue;

    case AboveMaxValue;

    case RoundsToZero;
}
