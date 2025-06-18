<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Model\ArithmeticRange;

/**
 * Implementation of PostgreSQL NUMRANGE data type.
 *
 * @see https://www.postgresql.org/docs/current/rangetypes.html
 * @since 3.1
 *
 * @author Jan Klan <jan@klan.com.au>
 */
class NumRange extends BaseType
{
    protected const TYPE_NAME = 'numrange';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ArithmeticRange
    {
        if (null === $value || 'empty' === $value) {
            return null;
        }

        if (!\is_string($value)) {
            throw new \RuntimeException('NumRange expects only string. Unexpected value from DB: '.$value);
        }

        if (!\preg_match('/(\[|\()(.*)\,(.*)(\]|\))/', $value, $matches)) {
            throw new \RuntimeException('unexpected value from DB: '.$value);
        }

        return ArithmeticRange::createFromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (empty($value)) {
            return null;
        }

        $stringValue = (string) $value;

        if ('(,)' === $stringValue) {
            return null;
        }

        return $stringValue;
    }
}
