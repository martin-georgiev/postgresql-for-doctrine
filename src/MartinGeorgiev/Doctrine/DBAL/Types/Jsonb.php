<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Implementation of PostgreSQL JSONB data type.
 *
 * @see https://www.postgresql.org/docs/9.4/static/datatype-json.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Jsonb extends BaseType
{
    use JsonTransformer;

    /**
     * @var string
     */
    protected const TYPE_NAME = 'jsonb';

    /**
     * Converts a value from its PHP representation to its database representation of the type.
     *
     * @param array|bool|float|int|string|null $value the value to convert
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->transformToPostgresJson($value);
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param string|null $value the value to convert
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): null|array|bool|float|int|string
    {
        if ($value === null) {
            return null;
        }

        return $this->transformFromPostgresJson($value);
    }
}
