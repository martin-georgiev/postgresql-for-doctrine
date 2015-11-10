<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Implementation of Postgres' text[] data type
 */
class TextArray extends Jsonb
{
    use JsonTransformer;

    /**
     * @var string
     */
    const TYPE_NAME = 'text[]';

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $convertedValue = parent::convertToDatabaseValue($value, $platform);
        return $convertedValue === '[]' ? '{}' : $convertedValue;
    }
}
