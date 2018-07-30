<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.11
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class AbstractIntegerArray extends AbstractTypeArray
{
    /**
     * @return string
     */
    abstract protected function getMinValue();

    /**
     * @return string
     */
    abstract protected function getMaxValue();

    /**
     * {@inheritDoc}
     */
    public function isValidArrayItemForDatabase($item)
    {
        return (is_int($item) || is_string($item))
            && (bool) preg_match('/^-?[0-9]+$/', (string) $item)
            && (string) $item >= $this->getMinValue()
            && (string) $item <= $this->getMaxValue();
    }

    /**
     * {@inheritDoc}
     */
    public function transformArrayItemForPHP($item)
    {
        if ($item === null) {
            return null;
        }

        $isInvalidPHPInt = (bool) preg_match('/^-?[0-9]+$/', (string) $item) === false
            || (string) $item < $this->getMinValue()
            || (string) $item > $this->getMaxValue();
        if ($isInvalidPHPInt) {
            throw new ConversionException(sprintf('Given value of %s content cannot be transformed to valid PHP integer.', var_export($item, true)));
        }

        return (int) $item;
    }
}
