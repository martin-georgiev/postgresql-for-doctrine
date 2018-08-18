<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonbSetTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSONB_SET' => JsonbSet::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT jsonb_set(c0_.object, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJson c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT JSONB_SET(e.object, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJson::class);
    }
}
