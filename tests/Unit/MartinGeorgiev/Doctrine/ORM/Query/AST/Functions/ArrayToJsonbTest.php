<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJsonb;

class ArrayToJsonbTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_JSONB' => ArrayToJsonb::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts array to jsonb' => 'SELECT array_to_jsonb(c0_.textArray) AS sclr_0 FROM ContainsArrays c0_',
            'converts array to jsonb with pretty' => "SELECT array_to_jsonb(c0_.textArray, 'true') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts array to jsonb' => \sprintf('SELECT ARRAY_TO_JSONB(e.textArray) FROM %s e', ContainsArrays::class),
            'converts array to jsonb with pretty' => \sprintf("SELECT ARRAY_TO_JSONB(e.textArray, 'true') FROM %s e", ContainsArrays::class),
        ];
    }
}

