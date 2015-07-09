<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of Postgres' jsonb[] data type
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
    protected function transformPostgresArrayToPHPArray($postgresArray) {
        $trimmedPostgresArray = mb_substr($postgresArray, 1, -1);
        if ($trimmedPostgresArray === '') {
            return [];
        }
        $phpArray = explode('},{', $trimmedPostgresArray);
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
