<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue;

class JsonValueTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_VALUE' => JsonValue::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts simple property value' => "SELECT json_value(c0_.jsonObject1, '$.name') AS sclr_0 FROM ContainsJsons c0_",
            'extracts nested property value' => "SELECT json_value(c0_.jsonObject1, '$.address.city') AS sclr_0 FROM ContainsJsons c0_",
            'extracts array element value' => "SELECT json_value(c0_.jsonObject1, '$.items[0]') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts simple property value' => \sprintf("SELECT JSON_VALUE(e.jsonObject1, '$.name') FROM %s e", ContainsJsons::class),
            'extracts nested property value' => \sprintf("SELECT JSON_VALUE(e.jsonObject1, '$.address.city') FROM %s e", ContainsJsons::class),
            'extracts array element value' => \sprintf("SELECT JSON_VALUE(e.jsonObject1, '$.items[0]') FROM %s e", ContainsJsons::class),
        ];
    }
}
