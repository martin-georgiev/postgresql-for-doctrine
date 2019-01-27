<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.11
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseIntegerArray extends BaseArray
{
    abstract protected function getMinValue(): string;

    abstract protected function getMaxValue(): string;

    public function isValidArrayItemForDatabase($item): bool
    {
        return (\is_int($item) || \is_string($item))
            && (bool) \preg_match('/^-?[0-9]+$/', (string) $item)
            && (string) $item >= $this->getMinValue()
            && (string) $item <= $this->getMaxValue();
    }

    /**
     * @param int|string|null $item Whole number
     */
    public function transformArrayItemForPHP($item): ?int
    {
        if ($item === null) {
            return null;
        }

        $isInvalidPHPInt = (bool) \preg_match('/^-?[0-9]+$/', (string) $item) === false
            || (string) $item < $this->getMinValue()
            || (string) $item > $this->getMaxValue();
        if ($isInvalidPHPInt) {
            throw new ConversionException(\sprintf('Given value of %s content cannot be transformed to valid PHP integer.', \var_export($item, true)));
        }

        return (int) $item;
    }
}
