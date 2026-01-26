<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildArray;

class JsonbBuildArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_BUILD_ARRAY' => JsonbBuildArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'builds empty array' => 'SELECT jsonb_build_array() AS sclr_0 FROM ContainsArrays c0_',
            'builds array with values' => "SELECT jsonb_build_array('a', 1, true) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'builds empty array' => \sprintf('SELECT JSONB_BUILD_ARRAY() FROM %s e', ContainsArrays::class),
            'builds array with values' => \sprintf("SELECT JSONB_BUILD_ARRAY('a', 1, true) FROM %s e", ContainsArrays::class),
        ];
    }
}

