<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OctetLength;

class OctetLengthTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OCTET_LENGTH' => OctetLength::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns byte length of a literal string' => "SELECT octet_length('hello') AS sclr_0 FROM ContainsTexts c0_",
            'returns byte length of a text field' => 'SELECT octet_length(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns byte length of a literal string' => \sprintf("SELECT OCTET_LENGTH('hello') FROM %s e", ContainsTexts::class),
            'returns byte length of a text field' => \sprintf('SELECT OCTET_LENGTH(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
