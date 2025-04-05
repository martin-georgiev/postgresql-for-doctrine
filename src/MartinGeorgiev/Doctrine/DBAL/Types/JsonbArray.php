<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Utils\PostgresJsonToPHPArrayTransformer;

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
        return PostgresJsonToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    /**
     * @param string $item
     */
    public function transformArrayItemForPHP($item): array
    {
        return PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPArray($item);
    }
}
