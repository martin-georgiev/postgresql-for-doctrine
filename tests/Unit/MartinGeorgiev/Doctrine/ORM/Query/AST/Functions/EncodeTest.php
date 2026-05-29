<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Encode;

class EncodeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ENCODE' => Encode::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'encodes a literal string to base64' => "SELECT encode('hello', 'base64') AS sclr_0 FROM ContainsTexts c0_",
            'encodes a text field to hex' => "SELECT encode(c0_.text1, 'hex') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'encodes a literal string to base64' => \sprintf("SELECT ENCODE('hello', 'base64') FROM %s e", ContainsTexts::class),
            'encodes a text field to hex' => \sprintf("SELECT ENCODE(e.text1, 'hex') FROM %s e", ContainsTexts::class),
        ];
    }
}
