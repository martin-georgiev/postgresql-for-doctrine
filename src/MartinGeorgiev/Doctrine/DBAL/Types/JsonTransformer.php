<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Helpers for converting PHP values into PostgreSql JSOn and vice versa
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait JsonTransformer
{
    /**
     * @param mixed $phpValue
     * 
     * @return string
     */
    public function transformToPostgresJson($phpValue)
    {
        return json_encode($phpValue);
    }
    
    /**
     * @param string $postgresValue
     *
     * @return array
     */
    public function transformFromPostgresJson($postgresValue)
    {
        return json_decode($postgresValue, true);
    }
}
