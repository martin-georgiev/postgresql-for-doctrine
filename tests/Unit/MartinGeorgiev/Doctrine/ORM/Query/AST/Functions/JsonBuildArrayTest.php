<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildArray;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText;

class JsonBuildArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_BUILD_ARRAY' => JsonBuildArray::class,
            'JSON_GET_FIELD_AS_TEXT' => JsonGetFieldAsText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'builds array with string values' => "SELECT json_build_array('a', 'b', 'c') AS sclr_0 FROM ContainsJsons c0_",
            'builds array with field values' => "SELECT json_build_array((c0_.jsonbObject1 ->> 'name'), (c0_.jsonbObject1 ->> 'age')) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'builds array with string values' => \sprintf("SELECT JSON_BUILD_ARRAY('a', 'b', 'c') FROM %s e", ContainsJsons::class),
            'builds array with field values' => \sprintf("SELECT JSON_BUILD_ARRAY(JSON_GET_FIELD_AS_TEXT(e.jsonbObject1, 'name'), JSON_GET_FIELD_AS_TEXT(e.jsonbObject1, 'age')) FROM %s e", ContainsJsons::class),
        ];
    }
}
