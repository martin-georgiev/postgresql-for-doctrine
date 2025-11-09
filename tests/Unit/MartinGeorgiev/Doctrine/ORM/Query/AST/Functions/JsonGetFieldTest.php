<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;

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
            'extracts top-level field from json' => "SELECT (c0_.jsonObject1 -> 'key') AS sclr_0 FROM ContainsJsons c0_",
            'extracts nested field from json' => "SELECT ((c0_.jsonObject1 -> 'nested') -> 'key') AS sclr_0 FROM ContainsJsons c0_",
            'extracts array element by index' => 'SELECT (c0_.jsonObject1 -> 0) AS sclr_0 FROM ContainsJsons c0_',
            'extracts nested array element by index' => "SELECT ((c0_.jsonObject1 -> 'tags') -> 1) AS sclr_0 FROM ContainsJsons c0_",
            'extracts field from array element by index' => "SELECT ((c0_.jsonObject1 -> 0) -> 'name') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts top-level field from json' => \sprintf("SELECT JSON_GET_FIELD(e.jsonObject1, 'key') FROM %s e", ContainsJsons::class),
            'extracts nested field from json' => \sprintf("SELECT JSON_GET_FIELD(JSON_GET_FIELD(e.jsonObject1, 'nested'), 'key') FROM %s e", ContainsJsons::class),
            'extracts array element by index' => \sprintf('SELECT JSON_GET_FIELD(e.jsonObject1, 0) FROM %s e', ContainsJsons::class),
            'extracts nested array element by index' => \sprintf("SELECT JSON_GET_FIELD(JSON_GET_FIELD(e.jsonObject1, 'tags'), 1) FROM %s e", ContainsJsons::class),
            'extracts field from array element by index' => \sprintf("SELECT JSON_GET_FIELD(JSON_GET_FIELD(e.jsonObject1, 0), 'name') FROM %s e", ContainsJsons::class),
        ];
    }
}
