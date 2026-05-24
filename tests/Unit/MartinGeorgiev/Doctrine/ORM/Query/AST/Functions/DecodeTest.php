<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Decode;

class DecodeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DECODE' => Decode::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'decodes a literal base64 string' => "SELECT decode('aGVsbG8=', 'base64') AS sclr_0 FROM ContainsTexts c0_",
            'decodes a text field from hex' => "SELECT decode(c0_.text1, 'hex') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'decodes a literal base64 string' => \sprintf("SELECT DECODE('aGVsbG8=', 'base64') FROM %s e", ContainsTexts::class),
            'decodes a text field from hex' => \sprintf("SELECT DECODE(e.text1, 'hex') FROM %s e", ContainsTexts::class),
        ];
    }
}
