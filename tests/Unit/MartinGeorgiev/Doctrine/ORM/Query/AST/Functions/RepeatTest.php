<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Repeat;

class RepeatTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REPEAT' => Repeat::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'repeats string' => 'SELECT repeat(c0_.text1, 2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'repeats string' => \sprintf('SELECT REPEAT(e.text1, 2) FROM %s e', ContainsTexts::class),
        ];
    }
}
