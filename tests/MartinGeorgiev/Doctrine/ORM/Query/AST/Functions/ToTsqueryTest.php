<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsTexts;

class ToTsqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT to_tsquery(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_tsquery(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_TSQUERY(e.text1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_TSQUERY(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
        ];
    }
}
