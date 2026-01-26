<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildArray;

class JsonBuildArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_BUILD_ARRAY' => JsonBuildArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'builds empty array' => 'SELECT json_build_array() AS sclr_0 FROM ContainsArrays c0_',
            'builds array with values' => "SELECT json_build_array('a', 1, true) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'builds empty array' => \sprintf('SELECT JSON_BUILD_ARRAY() FROM %s e', ContainsArrays::class),
            'builds array with values' => \sprintf("SELECT JSON_BUILD_ARRAY('a', 1, true) FROM %s e", ContainsArrays::class),
        ];
    }
}
