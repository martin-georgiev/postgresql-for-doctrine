<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CharLength;

class CharLengthTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CHAR_LENGTH' => CharLength::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns character length of a literal string' => "SELECT char_length('hello') AS sclr_0 FROM ContainsTexts c0_",
            'returns character length of a text field' => 'SELECT char_length(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns character length of a literal string' => \sprintf("SELECT CHAR_LENGTH('hello') FROM %s e", ContainsTexts::class),
            'returns character length of a text field' => \sprintf('SELECT CHAR_LENGTH(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
