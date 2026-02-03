<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Format;

class FormatTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FORMAT' => Format::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'formats single argument' => "SELECT format('Hello %s', c0_.text1) AS sclr_0 FROM ContainsTexts c0_",
            'formats multiple arguments' => "SELECT format('%s - %s', c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'formats single argument' => \sprintf("SELECT FORMAT('Hello %%s', e.text1) FROM %s e", ContainsTexts::class),
            'formats multiple arguments' => \sprintf("SELECT FORMAT('%%s - %%s', e.text1, e.text2) FROM %s e", ContainsTexts::class),
        ];
    }
}
