<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb;

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
            'converts text field to jsonb' => 'SELECT to_jsonb(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts function result to jsonb' => 'SELECT to_jsonb(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            'converts arithmetic expression to jsonb' => 'SELECT to_jsonb(1 + 1) AS sclr_0 FROM ContainsTexts c0_',
            'converts literal number to jsonb' => 'SELECT to_jsonb(1) AS sclr_0 FROM ContainsTexts c0_',
            'converts length function to jsonb' => 'SELECT to_jsonb(LENGTH(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts text field to jsonb' => \sprintf('SELECT TO_JSONB(e.text1) FROM %s e', ContainsTexts::class),
            'converts function result to jsonb' => \sprintf('SELECT TO_JSONB(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
            'converts arithmetic expression to jsonb' => \sprintf('SELECT TO_JSONB(1+1) FROM %s e', ContainsTexts::class),
            'converts literal number to jsonb' => \sprintf('SELECT TO_JSONB(true) FROM %s e', ContainsTexts::class),
            'converts length function to jsonb' => \sprintf('SELECT TO_JSONB(LENGTH(e.text1)) FROM %s e', ContainsTexts::class),
        ];
    }
}
