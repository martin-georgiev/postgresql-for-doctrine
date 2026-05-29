<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ascii;

class AsciiTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASCII' => Ascii::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns numeric code of a literal string' => "SELECT ascii('A') AS sclr_0 FROM ContainsTexts c0_",
            'returns numeric code of a text field' => 'SELECT ascii(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns numeric code of a literal string' => \sprintf("SELECT ASCII('A') FROM %s e", ContainsTexts::class),
            'returns numeric code of a text field' => \sprintf('SELECT ASCII(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
