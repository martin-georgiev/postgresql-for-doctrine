<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonGetObjectTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_OBJECT' => JsonGetObject::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.object #> '{residency}') AS sclr_0 FROM ContainsJson c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_GET_OBJECT(e.object, '{residency}') FROM %s e", ContainsJson::class),
        ];
    }
}
