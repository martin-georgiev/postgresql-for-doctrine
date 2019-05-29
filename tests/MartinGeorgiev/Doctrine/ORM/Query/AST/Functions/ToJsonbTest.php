<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

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
            'SELECT to_jsonb(c0_.text) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_jsonb(UPPER(c0_.text)) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_jsonb(1 + 1) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_jsonb(1) AS sclr_0 FROM ContainsText c0_',
            'SELECT to_jsonb(LENGTH(c0_.text)) AS sclr_0 FROM ContainsText c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_JSONB(e.text) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSONB(UPPER(e.text)) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSONB(1+1) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSONB(true) FROM %s e', ContainsText::class),
            \sprintf('SELECT TO_JSONB(LENGTH(e.text)) FROM %s e', ContainsText::class),
        ];
    }
}
