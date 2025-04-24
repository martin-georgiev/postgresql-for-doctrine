<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReturnsValueForJsonValue;

class ReturnsValueForJsonValueTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RETURNS_VALUE_FOR_JSON_VALUE' => ReturnsValueForJsonValue::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.object1 @?? '$.test[*] ?? (@ > 2)') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT RETURNS_VALUE_FOR_JSON_VALUE(e.object1, '$.test[*] ?? (@ > 2)') FROM %s e", ContainsJsons::class),
        ];
    }
}
