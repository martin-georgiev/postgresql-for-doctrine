<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseFloatArray extends BaseArray
{
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

        $stringValue = (string) $item;
        if (!\preg_match(self::FLOAT_REGEX, $stringValue)) {
            throw InvalidFloatArrayItemForDatabaseException::doesNotMatchRegex($item);
        }

        $floatValue = (float) $stringValue;

        // For scientific notation, convert to standard decimal form before checking precision
        if (\str_contains($stringValue, 'e') || \str_contains($stringValue, 'E')) {
            $standardForm = \sprintf('%.'.($this->getMaxPrecision() + 1).'f', $floatValue);
            $parts = \explode('.', $standardForm);
            if (isset($parts[1]) && \strlen($parts[1]) > $this->getMaxPrecision()) {
                throw InvalidFloatArrayItemForDatabaseException::isAScientificNotationWithExcessPrecision($item);
            }
        } elseif (\str_contains($stringValue, '.')) {
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
        if (!\preg_match(self::FLOAT_REGEX, $stringValue)) {
            throw InvalidFloatArrayItemForPHPException::forValueThatIsNotAValidPHPFloat($item, static::TYPE_NAME);
        }

        $floatValue = (float) $stringValue;

        // Check if value is too close to zero
        $absValue = \abs($floatValue);
        if ($absValue > 0 && $absValue < (float) $this->getMinAbsoluteValue()) {
            throw InvalidFloatArrayItemForPHPException::forValueThatIsTooCloseToZero($item, static::TYPE_NAME);
        }

        if ($floatValue < (float) $this->getMinValue() || $floatValue > (float) $this->getMaxValue()) {
            throw InvalidFloatArrayItemForPHPException::forValueThatIsNotAValidPHPFloat($item, static::TYPE_NAME);
        }

        // Scientific notation is valid for input as long as the resulting number
        // when converted to decimal doesn't exceed precision limits
        if (\str_contains($stringValue, 'e') || \str_contains($stringValue, 'E')) {
            return $floatValue;
        }

        // For regular decimal notation, check precision
        if (\str_contains($stringValue, '.')) {
            $parts = \explode('.', $stringValue);
            if (\strlen($parts[1]) > $this->getMaxPrecision()) {
                throw InvalidFloatArrayItemForPHPException::forValueThatExceedsMaximumPrecision($item, static::TYPE_NAME);
            }
        }

        return $floatValue;
    }
}
