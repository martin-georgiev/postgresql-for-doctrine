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
            'extracts top-level field from json' => "SELECT (c0_.object1 -> 'key') AS sclr_0 FROM ContainsJsons c0_",
            'extracts nested field from json' => "SELECT ((c0_.object1 -> 'nested') -> 'key') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts top-level field from json' => \sprintf("SELECT JSON_GET_FIELD(e.object1, 'key') FROM %s e", ContainsJsons::class),
            'extracts nested field from json' => \sprintf("SELECT JSON_GET_FIELD(JSON_GET_FIELD(e.object1, 'nested'), 'key') FROM %s e", ContainsJsons::class),
        ];
    }
}
