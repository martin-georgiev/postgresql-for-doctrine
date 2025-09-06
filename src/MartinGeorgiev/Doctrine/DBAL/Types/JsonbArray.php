<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use MartinGeorgiev\Utils\PostgresJsonToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL JSONB[] data type.
 *
 * @see https://www.postgresql.org/docs/17/arrays.html
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
        // Quote each JSON value as a PostgreSQL array element and escape inner quotes and backslashes
        $json = $this->transformToPostgresJson($item);
        $escaped = \str_replace(['\\', '"'], ['\\\\', '\\"'], $json);

        return '"'.$escaped.'"';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        $trimmed = \trim($postgresArray);
        if ($trimmed === '{}') {
            return [];
        }

        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    /**
     * @param string $item
     */
    public function transformArrayItemForPHP($item): array
    {
        return PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPArray($item);
    }
}
