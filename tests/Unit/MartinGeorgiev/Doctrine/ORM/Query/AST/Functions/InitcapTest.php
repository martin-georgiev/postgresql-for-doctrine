<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Initcap;

class InitcapTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INITCAP' => Initcap::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'capitalizes first letter of each word in a literal string' => "SELECT initcap('hello world') AS sclr_0 FROM ContainsTexts c0_",
            'capitalizes first letter of each word in a text field' => 'SELECT initcap(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'capitalizes first letter of each word in a literal string' => \sprintf("SELECT INITCAP('hello world') FROM %s e", ContainsTexts::class),
            'capitalizes first letter of each word in a text field' => \sprintf('SELECT INITCAP(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
