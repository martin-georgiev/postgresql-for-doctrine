<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use MartinGeorgiev\Utils\PostgresFloat;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseFloatArray extends BaseArray
{
    /**
     * @var string
     */
    private const FLOAT_REGEX = '/^-?\d*\.?\d+(?:[eE][-+]?\d+)?\z/';

    abstract protected function getMinValue(): string;

    abstract protected function getMaxValue(): string;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        try {
            $this->throwIfInvalidArrayItemForDatabase($item);
        } catch (InvalidFloatArrayItemForDatabaseException) {
            return false;
        }

        return true;
    }

    private function throwIfInvalidArrayItemForDatabase(mixed $item): void
    {
        if ($item === null) {
            return;
        }

        $isNotANumber = !\is_float($item) && !\is_int($item) && !\is_string($item);
        if ($isNotANumber) {
            throw InvalidFloatArrayItemForDatabaseException::isNotANumber($item);
        }

        // Infinity and NaN are values PostgreSQL stores and emits.
        // The range and rounds-to-zero checks below describe finite numbers.
        if (\is_float($item) && !\is_finite($item)) {
            return;
        }

        $stringValue = \is_float($item) ? PostgresFloat::format($item) : (string) $item;
        if (PostgresFloat::isNonFinite($stringValue)) {
            return;
        }

        if (!\preg_match(self::FLOAT_REGEX, $stringValue)) {
            throw InvalidFloatArrayItemForDatabaseException::doesNotMatchRegex($item);
        }

        $floatValue = (float) $stringValue;

        if ($this->isBelowMinValue($stringValue, $floatValue)) {
            throw InvalidFloatArrayItemForDatabaseException::isBelowMinValue($item);
        }

        if ($this->isAboveMaxValue($stringValue, $floatValue)) {
            throw InvalidFloatArrayItemForDatabaseException::isAboveMaxValue($item);
        }

        if ($this->roundsToZero($stringValue, $floatValue)) {
            throw InvalidFloatArrayItemForDatabaseException::absoluteValueIsTooCloseToZero($item);
        }
    }

    protected function isBelowMinValue(string $value, float $floatValue): bool
    {
        return $floatValue < (float) $this->getMinValue();
    }

    protected function isAboveMaxValue(string $value, float $floatValue): bool
    {
        return $floatValue > (float) $this->getMaxValue();
    }

    /**
     * PostgreSQL rounds a value to the nearest one its type holds, subnormals included, and rejects only a non-zero value
     * that rounds to zero. A PHP float is a double, so a value that rounds to zero in double precision parses as zero.
     */
    protected function roundsToZero(string $value, float $floatValue): bool
    {
        return $floatValue === 0.0 && PostgresFloat::compareMagnitudes($value, '0') !== 0;
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        $this->throwIfInvalidArrayItemForDatabase($item);

        throw InvalidFloatArrayItemForDatabaseException::isNotANumber($item);
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (\is_float($item)) {
            return PostgresFloat::format($item);
        }

        \assert(\is_scalar($item));

        return (string) $item;
    }

    public function transformArrayItemForPHP(mixed $item): ?float
    {
        if ($item === null) {
            return null;
        }

        $isNotANumberCandidate = !\is_float($item) && !\is_int($item) && !\is_string($item);
        if ($isNotANumberCandidate) {
            throw InvalidFloatArrayItemForPHPException::forValueThatIsNotAValidPHPFloat($item, static::TYPE_NAME);
        }

        $stringValue = (string) $item;
        if (PostgresFloat::isNonFinite($stringValue)) {
            return PostgresFloat::parse($stringValue);
        }

        if (!\preg_match(self::FLOAT_REGEX, $stringValue)) {
            throw InvalidFloatArrayItemForPHPException::forValueThatIsNotAValidPHPFloat($item, static::TYPE_NAME);
        }

        $floatValue = (float) $stringValue;

        if ($floatValue < (float) $this->getMinValue() || $floatValue > (float) $this->getMaxValue()) {
            throw InvalidFloatArrayItemForPHPException::forValueThatIsNotAValidPHPFloat($item, static::TYPE_NAME);
        }

        return $floatValue;
    }
}
