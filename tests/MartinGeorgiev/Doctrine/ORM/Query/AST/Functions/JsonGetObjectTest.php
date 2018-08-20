<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonGetObjectTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSON_GET_OBJECT' => JsonGetObject::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT (c0_.object #> '{residency}') AS sclr_0 FROM ContainsJson c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT JSON_GET_OBJECT(e.object, '{residency}') FROM %s e", ContainsJson::class);
    }
}
