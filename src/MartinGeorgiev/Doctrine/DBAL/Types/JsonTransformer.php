<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

trait JsonTransformer
{
    /**
     * @param mixed $phpValue
     * @return string
     */
    public function transformToPostgresJson($phpValue)
    {
        return json_encode($phpValue);
    }
    
    /**
     * @param string $postgresValue
     * @return array
     */
    public function transformFromPostgresJson($postgresValue)
    {
        return json_decode($postgresValue, true);
    }
}
