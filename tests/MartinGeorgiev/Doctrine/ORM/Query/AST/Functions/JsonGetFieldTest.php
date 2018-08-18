<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonGetFieldTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT (c0_.object -> 'country') AS sclr_0 FROM ContainsJson c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT JSON_GET_FIELD(e.object, 'country') FROM %s e", ContainsJson::class);
    }
}
