<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat;

class StrConcatTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRCONCAT' => StrConcat::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.text1 || 'text2') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT STRCONCAT(e.text1, 'text2') FROM %s e", ContainsTexts::class),
        ];
    }
}
