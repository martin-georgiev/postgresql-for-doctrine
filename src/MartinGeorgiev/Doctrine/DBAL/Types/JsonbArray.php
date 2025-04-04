<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonbArrayItemForPHPException;

/**
 * Implementation of PostgreSQL JSONB[] data type.
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbArray extends BaseArray
{
    use JsonTransformer;

    protected const TYPE_NAME = 'jsonb[]';

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        return $this->transformToPostgresJson($item);
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        if ($postgresArray === '{}') {
            return [];
        }

        $trimmedPostgresArray = \mb_substr($postgresArray, 2, -2);
        $phpArray = \explode('},{', $trimmedPostgresArray);
        foreach ($phpArray as &$item) {
            $item = '{'.$item.'}';
        }

        return $phpArray;
    }

    /**
     * @param string $item
     */
    public function transformArrayItemForPHP($item): array
    {
        try {
            $transformedValue = $this->transformFromPostgresJson($item);
            if (!\is_array($transformedValue)) {
                throw InvalidJsonbArrayItemForPHPException::forInvalidType($item);
            }

            return $transformedValue;
        } catch (\JsonException) {
            throw InvalidJsonbArrayItemForPHPException::forInvalidFormat($item);
        }
    }
}
