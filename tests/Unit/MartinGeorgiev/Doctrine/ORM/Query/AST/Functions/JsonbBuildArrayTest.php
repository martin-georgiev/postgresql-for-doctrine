<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildArray;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText;

class JsonbBuildArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_BUILD_ARRAY' => JsonbBuildArray::class,
            'JSON_GET_FIELD_AS_TEXT' => JsonGetFieldAsText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'builds array with string values' => "SELECT jsonb_build_array('a', 'b', 'c') AS sclr_0 FROM ContainsJsons c0_",
            'builds array with field values' => "SELECT jsonb_build_array((c0_.jsonbObject1 ->> 'name'), (c0_.jsonbObject1 ->> 'age')) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'builds array with string values' => \sprintf("SELECT JSONB_BUILD_ARRAY('a', 'b', 'c') FROM %s e", ContainsJsons::class),
            'builds array with field values' => \sprintf("SELECT JSONB_BUILD_ARRAY(JSON_GET_FIELD_AS_TEXT(e.jsonbObject1, 'name'), JSON_GET_FIELD_AS_TEXT(e.jsonbObject1, 'age')) FROM %s e", ContainsJsons::class),
        ];
    }
}
