<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Casefold;

class CasefoldTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CASEFOLD' => Casefold::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'folds a string to lowercase' => "SELECT casefold('Hello Doctrine') AS sclr_0 FROM ContainsTexts c0_",
            'folds text field to lowercase' => 'SELECT casefold(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'folds a string to lowercase' => \sprintf("SELECT CASEFOLD('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'folds text field to lowercase' => \sprintf('SELECT CASEFOLD(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
