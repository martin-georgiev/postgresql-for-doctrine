<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

trait JsonTransformer
{
    /**
     * @param mixed $phpValue
     * @return string
     */
    protected function transformToPostgresJson($phpValue)
    {
        return json_encode($phpValue);
    }
    
    /**
     * @param string $postgresValue
     * @return array
     */
    protected function transformFromPostgresJson($postgresValue)
    {
        return json_decode($postgresValue, true);
    }
}
