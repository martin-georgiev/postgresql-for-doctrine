<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Md5;

class Md5Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MD5' => Md5::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes md5 of a string' => "SELECT md5('Hello Doctrine') AS sclr_0 FROM ContainsTexts c0_",
            'computes md5 of text field' => 'SELECT md5(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes md5 of a string' => \sprintf("SELECT MD5('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'computes md5 of text field' => \sprintf('SELECT MD5(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
