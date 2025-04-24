<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;

class CastTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'cast as integer' => 'SELECT cast(c0_.text1 as INTEGER) AS sclr_0 FROM ContainsTexts c0_',
            'cast as text' => 'SELECT cast(c0_.text1 as TEXT) AS sclr_0 FROM ContainsTexts c0_',
            'cast as json' => 'SELECT cast(c0_.text1 as JSON) AS sclr_0 FROM ContainsTexts c0_',
            'cast as jsonb' => 'SELECT cast(c0_.text1 as JSONB) AS sclr_0 FROM ContainsTexts c0_',
            'cast as boolean' => 'SELECT cast(c0_.text1 as BOOLEAN) AS sclr_0 FROM ContainsTexts c0_',
            'cast with precision' => 'SELECT cast(c0_.text1 as DECIMAL(10, 2)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'cast as integer' => \sprintf('SELECT CAST(e.text1 AS INTEGER) FROM %s e', ContainsTexts::class),
            'cast as text' => \sprintf('SELECT CAST(e.text1 AS TEXT) FROM %s e', ContainsTexts::class),
            'cast as json' => \sprintf('SELECT CAST(e.text1 AS JSON) FROM %s e', ContainsTexts::class),
            'cast as jsonb' => \sprintf('SELECT CAST(e.text1 AS JSONB) FROM %s e', ContainsTexts::class),
            'cast as boolean' => \sprintf('SELECT CAST(e.text1 AS BOOLEAN) FROM %s e', ContainsTexts::class),
            'cast with precision' => \sprintf('SELECT CAST(e.text1 AS DECIMAL(10, 2)) FROM %s e', ContainsTexts::class),
        ];
    }
}
