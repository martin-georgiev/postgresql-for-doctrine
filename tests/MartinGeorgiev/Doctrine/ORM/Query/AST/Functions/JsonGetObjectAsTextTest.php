<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonGetObjectAsTextTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSON_GET_OBJECT_AS_TEXT' => JsonGetObjectAsText::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT (c0_.object #>> '{residency,country}') AS sclr_0 FROM ContainsJson c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object, '{residency,country}') FROM %s e", ContainsJson::class);
    }
}
