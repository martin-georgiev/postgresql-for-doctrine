<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strpos;

class StrposTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRPOS' => Strpos::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'finds position of a substring in a literal string' => "SELECT strpos('hello world', 'world') AS sclr_0 FROM ContainsTexts c0_",
            'finds position of a substring in a text field' => "SELECT strpos(c0_.text1, 'search') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds position of a substring in a literal string' => \sprintf("SELECT STRPOS('hello world', 'world') FROM %s e", ContainsTexts::class),
            'finds position of a substring in a text field' => \sprintf("SELECT STRPOS(e.text1, 'search') FROM %s e", ContainsTexts::class),
        ];
    }
}
