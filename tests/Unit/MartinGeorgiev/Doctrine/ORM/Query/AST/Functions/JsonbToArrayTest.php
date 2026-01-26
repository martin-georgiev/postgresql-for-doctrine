<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbToArray;

class JsonbToArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TO_ARRAY' => JsonbToArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts jsonb to array' => 'SELECT jsonb_to_array(c0_.jsonbObject1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts jsonb to array' => \sprintf('SELECT JSONB_TO_ARRAY(e.jsonbObject1) FROM %s e', ContainsJsons::class),
        ];
    }
}
