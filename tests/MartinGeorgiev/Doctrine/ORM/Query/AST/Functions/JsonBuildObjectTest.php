<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject;

class JsonBuildObjectTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_BUILD_OBJECT' => JsonBuildObject::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT json_build_object('key1', c0_.object1) AS sclr_0 FROM ContainsJsons c0_",
            "SELECT json_build_object('key1', UPPER('value1'), 'key2', 'value2') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT json_build_object('key1', c0_.object1, 'key2', c0_.object2) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.object1) FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_BUILD_OBJECT('key1', UPPER('value1'), 'key2', 'value2') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.object1, 'key2', e.object2) FROM %s e", ContainsJsons::class),
        ];
    }
}
