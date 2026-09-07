<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForPHPException;

/**
 * Implementation of PostgreSQL JSON[] data type.
 *
 * Unlike JSONB[], PostgreSQL stores JSON values as exact textual copies, preserving key order and whitespace.
 * The PHP-side conversion is identical to JSONB[].
 *
 * @see https://www.postgresql.org/docs/18/datatype-json.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonArray extends JsonbArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::JSON_ARRAY;

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidJsonArrayItemForPHPException::forInvalidArrayType($value);
    }
}
