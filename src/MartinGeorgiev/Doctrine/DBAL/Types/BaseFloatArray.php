<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresFloatConversionTrait;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseFloatArray extends BaseArray
{
    use PostgresFloatConversionTrait;

    /**
     * @var string
     */
    private const FLOAT_REGEX = '/^-?\d*\.?\d+(?:[eE][-+]?\d+)?$/';

    abstract protected function getMinValue(): string;

    abstract protected function getMaxValue(): string;

    abstract protected function getMaxPrecision(): int;

    abstract protected function getMinAbsoluteValue(): string;

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
        $isNotANumber = !\is_float($item) && !\is_int($item) && !\is_string($item);
        if ($isNotANumber) {
            throw InvalidFloatArrayItemForDatabaseException::isNotANumber($item);
        }

        // Infinity and NaN are values PostgreSQL stores and emits.
        // The precision, range and closeness-to-zero checks below all describe finite numbers.
        if (\is_float($item) && !\is_finite($item)) {
            return;
        }

        $stringValue = (string) $item;
        if (self::isNonFiniteString($stringValue)) {
            return;
        }

        if (!\preg_match(self::FLOAT_REGEX, $stringValue)) {
            throw InvalidFloatArrayItemForDatabaseException::doesNotMatchRegex($item);
        }

        $floatValue = (float) $stringValue;

        $isScientificNotation = \str_contains($stringValue, 'e') || \str_contains($stringValue, 'E');
        if (!$isScientificNotation && \str_contains($stringValue, '.')) {
            $parts = \explode('.', $stringValue);
            if (\strlen($parts[1]) > $this->getMaxPrecision()) {
                throw InvalidFloatArrayItemForDatabaseException::isANormalNumberWithExcessPrecision($item);
            }
        }

        $isBelowMinValue = $floatValue < (float) $this->getMinValue();
        if ($isBelowMinValue) {
            throw InvalidFloatArrayItemForDatabaseException::isBelowMinValue($item);
        }

        $isAboveMaxValue = $floatValue > (float) $this->getMaxValue();
        if ($isAboveMaxValue) {
            throw InvalidFloatArrayItemForDatabaseException::isAboveMaxValue($item);
        }

        // Check if value is too close to zero
        $absoluteValue = \abs($floatValue);
        $isTooCloseToZero = $absoluteValue > 0 && $absoluteValue < (float) $this->getMinAbsoluteValue();
        if ($isTooCloseToZero) {
            throw InvalidFloatArrayItemForDatabaseException::absoluteValueIsTooCloseToZero($item);
        }
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        $this->throwIfInvalidArrayItemForDatabase($item);

        throw InvalidFloatArrayItemForDatabaseException::isNotANumber($item);
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if (\is_float($item)) {
            return self::formatFloat($item);
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
        if (self::isNonFiniteString($stringValue)) {
            return self::parseFloat($stringValue);
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
