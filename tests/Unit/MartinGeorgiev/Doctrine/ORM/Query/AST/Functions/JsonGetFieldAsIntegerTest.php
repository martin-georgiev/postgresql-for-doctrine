<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger;

class JsonGetFieldAsIntegerTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
            'JSON_GET_FIELD_AS_INTEGER' => JsonGetFieldAsInteger::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts field as integer' => "SELECT CAST(c0_.jsonObject1 ->> 'rank' as BIGINT) AS sclr_0 FROM ContainsJsons c0_",
            'extracts array element as integer' => 'SELECT CAST(c0_.jsonObject1 ->> 0 as BIGINT) AS sclr_0 FROM ContainsJsons c0_',
            'extracts nested array element as integer' => "SELECT CAST((c0_.jsonObject1 -> 'scores') ->> 1 as BIGINT) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts field as integer' => \sprintf("SELECT JSON_GET_FIELD_AS_INTEGER(e.jsonObject1, 'rank') FROM %s e", ContainsJsons::class),
            'extracts array element as integer' => \sprintf('SELECT JSON_GET_FIELD_AS_INTEGER(e.jsonObject1, 0) FROM %s e', ContainsJsons::class),
            'extracts nested array element as integer' => \sprintf("SELECT JSON_GET_FIELD_AS_INTEGER(JSON_GET_FIELD(e.jsonObject1, 'scores'), 1) FROM %s e", ContainsJsons::class),
        ];
    }
}
