<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString;

class ArrayToStringTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_STRING' => ArrayToString::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts array to string with delimiter' => "SELECT array_to_string(c0_.array1, ',') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts array to string with delimiter' => \sprintf("SELECT ARRAY_TO_STRING(e.array1, ',') FROM %s e", ContainsArrays::class),
        ];
    }
}
