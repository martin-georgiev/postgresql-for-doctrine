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
            "SELECT json_value(c0_.object1, '$.name') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT json_value(c0_.object1, '$.address.city') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT json_value(c0_.object1, '$.items[0]') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_VALUE(e.object1, '$.name') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_VALUE(e.object1, '$.address.city') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_VALUE(e.object1, '$.items[0]') FROM %s e", ContainsJsons::class),
        ];
    }
}
