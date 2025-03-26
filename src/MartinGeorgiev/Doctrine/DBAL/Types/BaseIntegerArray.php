<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;

/**
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.11
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseIntegerArray extends BaseArray
{
    private const INTEGER_REGEX = '/^-?\d+$/';

    abstract protected function getMinValue(): int;

    abstract protected function getMaxValue(): int;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        try {
            $this->throwIfInvalidArrayItemForDatabase($item);
        } catch (InvalidIntegerArrayItemForDatabaseException) {
            return false;
        }

        return true;
    }

    private function throwIfInvalidArrayItemForDatabase(mixed $item): void
    {
        $isNotANumber = !\is_int($item) && !\is_string($item);
        if ($isNotANumber) {
            throw InvalidIntegerArrayItemForDatabaseException::isNotANumber($item);
        }

        $stringValue = (string) $item;
        if (!\preg_match(self::INTEGER_REGEX, $stringValue)) {
            throw InvalidIntegerArrayItemForDatabaseException::doesNotMatchRegex($item);
        }

        $doesNotFitIntoPHPInteger = $stringValue !== (string) (int) $stringValue;
        if ($doesNotFitIntoPHPInteger) {
            throw InvalidIntegerArrayItemForDatabaseException::isOutOfRange($item);
        }

        $integerValue = (int) $item;
        if ($integerValue < $this->getMinValue()) {
            throw InvalidIntegerArrayItemForDatabaseException::isBelowMinValue($item);
        }

        if ($integerValue > $this->getMaxValue()) {
            throw InvalidIntegerArrayItemForDatabaseException::isAboveMaxValue($item);
        }
    }

    public function transformArrayItemForPHP(mixed $item): ?int
    {
        if ($item === null) {
            return null;
        }

        $isNotANumberCandidate = !\is_int($item) && !\is_string($item);
        if ($isNotANumberCandidate) {
            throw InvalidIntegerArrayItemForPHPException::forValueThatIsNotAValidPHPInteger($item, static::TYPE_NAME);
        }

        $stringValue = (string) $item;
        if (!\preg_match(self::INTEGER_REGEX, $stringValue)) {
            throw InvalidIntegerArrayItemForPHPException::forValueThatIsNotAValidPHPInteger($item, static::TYPE_NAME);
        }

        $doesNotFitIntoPHPInteger = $stringValue !== (string) (int) $stringValue;
        if ($doesNotFitIntoPHPInteger) {
            throw InvalidIntegerArrayItemForPHPException::forValueOutOfRangeInPHP($item, static::TYPE_NAME);
        }

        $doesNotFitIntoDatabaseInteger = $stringValue < $this->getMinValue() || $stringValue > $this->getMaxValue();
        if ($doesNotFitIntoDatabaseInteger) {
            throw InvalidIntegerArrayItemForPHPException::forValueOutOfRangeInDatabaseType($item, static::TYPE_NAME);
        }

        return (int) $item;
    }
}
