<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Translate;

class TranslateTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRANSLATE' => Translate::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'translates characters in a literal string' => "SELECT translate('hello', 'aeiou', '12345') AS sclr_0 FROM ContainsTexts c0_",
            'translates characters in a text field' => "SELECT translate(c0_.text1, 'abc', '123') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'translates characters in a literal string' => \sprintf("SELECT TRANSLATE('hello', 'aeiou', '12345') FROM %s e", ContainsTexts::class),
            'translates characters in a text field' => \sprintf("SELECT TRANSLATE(e.text1, 'abc', '123') FROM %s e", ContainsTexts::class),
        ];
    }
}
