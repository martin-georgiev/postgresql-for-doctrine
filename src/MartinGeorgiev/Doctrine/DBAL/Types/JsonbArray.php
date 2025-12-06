<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
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

    protected const TYPE_NAME = Type::JSONB_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        // Quote each JSON value as a PostgreSQL array element and escape inner quotes and backslashes
        $escaped = \strtr(
            $this->transformToPostgresJson($item),
            ['\\' => '\\\\', '"' => '\\"']
        );

        return '"'.$escaped.'"';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
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
