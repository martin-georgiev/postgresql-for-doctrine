<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use stdClass;

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
     * @return stdClass
     */
    protected function transformFromPostgresJson($postgresValue)
    {
        return json_decode($postgresValue);
    }
}
