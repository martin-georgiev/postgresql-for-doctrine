<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSql JSONB[] data type
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbArray extends AbstractTypeArray
{
    use JsonTransformer;

    /**
     * @var string
     */
    const TYPE_NAME = 'jsonb[]';

    /**
     * {@inheritDoc}
     */
    protected function transformArrayItemForPostgres($item)
    {
        $json = $this->transformToPostgresJson($item);

        return $json;
    }

    /**
     * {@inheritDoc}
     */
    protected function transformPostgresArrayToPHPArray($postgresArray)
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

    /**
     * {@inheritDoc}
     */
    public function transformArrayItemForPHP($item)
    {
        return $this->transformFromPostgresJson($item);
    }
}
