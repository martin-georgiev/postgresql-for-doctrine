<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject;

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
            "SELECT (c0_.object1 #> '{residency}') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_GET_OBJECT(e.object1, '{residency}') FROM %s e", ContainsJsons::class),
        ];
    }
}
