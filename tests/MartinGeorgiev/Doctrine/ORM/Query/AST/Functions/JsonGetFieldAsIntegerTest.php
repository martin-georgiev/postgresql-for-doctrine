<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonGetFieldAsIntegerTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD_AS_INTEGER' => JsonGetFieldAsInteger::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT CAST(c0_.object ->> 'rank' as BIGINT) AS sclr_0 FROM ContainsJson c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf("SELECT JSON_GET_FIELD_AS_INTEGER(e.object, 'rank') FROM %s e", ContainsJson::class),
        ];
    }
}
