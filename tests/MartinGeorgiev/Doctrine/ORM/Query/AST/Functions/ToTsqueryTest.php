<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

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
            'SELECT to_tsquery(c0_.text) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_tsquery(UPPER(c0_.text)) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_tsquery(1 + 1) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_tsquery(1) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_tsquery(LENGTH(c0_.text)) AS sclr_0 FROM ContainsText c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_TSQUERY(e.text) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_TSQUERY(UPPER(e.text)) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_TSQUERY(1+1) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_TSQUERY(true) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_TSQUERY(LENGTH(e.text)) FROM %s e', ContainsText::class),
        ];
    }
}
