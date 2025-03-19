<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson;

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
            'converts simple array to json' => 'SELECT array_to_json(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts simple array to json' => \sprintf('SELECT ARRAY_TO_JSON(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
