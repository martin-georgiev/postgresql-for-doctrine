<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate;

class ToDateTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_DATE' => ToDate::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT to_date(c0_.text1, 'DD Mon YYYY') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT TO_DATE(e.text1, 'DD Mon YYYY') FROM %s e", ContainsTexts::class),
        ];
    }
}
