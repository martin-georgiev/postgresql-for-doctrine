<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonGetFieldTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.object -> 'country') AS sclr_0 FROM ContainsJson c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_GET_FIELD(e.object, 'country') FROM %s e", ContainsJson::class),
        ];
    }
}
