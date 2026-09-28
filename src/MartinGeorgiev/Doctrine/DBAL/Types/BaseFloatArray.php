<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

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
    private const FLOAT_REGEX = '/^-?\d*\.?\d+(?:[eE][-+]?\d+)?$/';

    abstract protected function getMinValue(): string;

    abstract protected function getMaxValue(): string;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return !$this->findProblem($item) instanceof FloatArrayItemProblem;
    }

    private function findProblem(mixed $item): ?FloatArrayItemProblem
    {
        if ($item === null) {
            return null;
        }

        $isNotANumber = !\is_float($item) && !\is_int($item) && !\is_string($item);
        if ($isNotANumber) {
            return FloatArrayItemProblem::NotANumber;
        }

        // Infinity and NaN are values PostgreSQL stores and emits.
        // The range and rounds-to-zero checks below describe finite numbers.
        if (\is_float($item) && !\is_finite($item)) {
            return null;
        }

        $stringValue = \is_float($item) ? PostgresFloat::format($item) : (string) $item;
        if (PostgresFloat::isNonFinite($stringValue)) {
            return null;
        }

        if (!\preg_match(self::FLOAT_REGEX, $stringValue)) {
            return FloatArrayItemProblem::NotAFloatLiteral;
        }

        $floatValue = (float) $stringValue;

        if ($this->isBelowMinValue($stringValue, $floatValue)) {
            return FloatArrayItemProblem::BelowMinValue;
        }

        if ($this->isAboveMaxValue($stringValue, $floatValue)) {
            return FloatArrayItemProblem::AboveMaxValue;
        }

        if ($this->roundsToZero($stringValue, $floatValue)) {
            return FloatArrayItemProblem::RoundsToZero;
        }

        return null;
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
        throw ($this->findProblem($item) ?? FloatArrayItemProblem::NotANumber)->toException($item);
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
