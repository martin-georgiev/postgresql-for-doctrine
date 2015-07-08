<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

trait JsonTransformer
{
    protected function transformForPostgres($phpValue)
    {
        return json_encode($phpValue);
    }
    
    protected function transformForPHP($postgresValue)
    {
        return json_decode($postgresValue);
    }
}
