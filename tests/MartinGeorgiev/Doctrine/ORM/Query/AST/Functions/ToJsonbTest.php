<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsTexts;

class ToJsonbTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_JSONB' => ToJsonb::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT to_jsonb(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_jsonb(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_jsonb(1 + 1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_jsonb(1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_jsonb(LENGTH(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_JSONB(e.text1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSONB(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSONB(1+1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSONB(true) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_JSONB(LENGTH(e.text1)) FROM %s e', ContainsTexts::class),
        ];
    }
}
