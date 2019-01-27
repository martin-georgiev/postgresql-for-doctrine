<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonbInsertTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_INSERT' => JsonbInsert::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT jsonb_insert(c0_.object, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJson c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSONB_INSERT(e.object, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJson::class),
        ];
    }
}
