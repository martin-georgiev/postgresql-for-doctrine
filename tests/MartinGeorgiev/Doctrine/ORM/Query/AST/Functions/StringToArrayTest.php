<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

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
            "SELECT string_to_array(c0_.text, ',') AS sclr_0 FROM ContainsText c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT STRING_TO_ARRAY(e.text, ',') FROM %s e", ContainsText::class),
        ];
    }
}
