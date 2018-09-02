<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSql JSONB[] data type
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbArray extends BaseArray
{
    use JsonTransformer;

    /**
     * @var string
     */
    protected const TYPE_NAME = 'jsonb[]';

    protected function transformArrayItemForPostgres($item): string
    {
        return $this->transformToPostgresJson($item);
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        if ($postgresArray === '{}') {
            return [];
        }
        $trimmedPostgresArray = mb_substr($postgresArray, 2, -2);
        $phpArray = explode('},{', $trimmedPostgresArray);
        foreach ($phpArray as &$item) {
            $item = '{'.$item.'}';
        }

        return $phpArray;
    }

    public function transformArrayItemForPHP($item): array
    {
        return $this->transformFromPostgresJson($item);
    }
}
