<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArrays;

class ArrayToJsonTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_JSON' => ArrayToJson::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_to_json(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_TO_JSON(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
