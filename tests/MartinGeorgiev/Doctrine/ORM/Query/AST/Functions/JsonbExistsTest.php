<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonbExistsTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSONB_EXISTS' => JsonbExists::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT jsonb_exists(c0_.object, 'country') AS sclr_0 FROM ContainsJson c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT JSONB_EXISTS(e.object, 'country') FROM %s e", ContainsJson::class);
    }
}
