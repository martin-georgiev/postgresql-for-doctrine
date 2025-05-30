<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger;

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
            "SELECT CAST(c0_.object1 ->> 'rank' as BIGINT) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_GET_FIELD_AS_INTEGER(e.object1, 'rank') FROM %s e", ContainsJsons::class),
        ];
    }
}
