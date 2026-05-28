<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateDiff;

class DateDiffTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_DIFF' => DateDiff::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns difference between timestamps in given unit' => "SELECT date_diff('day', c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_",
            'returns difference between literal timestamps' => "SELECT date_diff('hour', '2024-01-01', '2024-01-02') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns difference between timestamps in given unit' => \sprintf("SELECT DATE_DIFF('day', e.text1, e.text2) FROM %s e", ContainsTexts::class),
            'returns difference between literal timestamps' => \sprintf("SELECT DATE_DIFF('hour', '2024-01-01', '2024-01-02') FROM %s e", ContainsTexts::class),
        ];
    }
}
