<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray;

class StringToArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRING_TO_ARRAY' => StringToArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT string_to_array(c0_.text1, ',') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT STRING_TO_ARRAY(e.text1, ',') FROM %s e", ContainsTexts::class),
        ];
    }
}
