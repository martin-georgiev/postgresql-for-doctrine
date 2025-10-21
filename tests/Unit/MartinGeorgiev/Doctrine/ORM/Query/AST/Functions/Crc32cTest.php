<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32c;

class Crc32cTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CRC32C' => Crc32c::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes crc32c of a string' => "SELECT crc32c('Hello Doctrine') AS sclr_0 FROM ContainsTexts c0_",
            'computes crc32c of text field' => 'SELECT crc32c(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes crc32c of a string' => \sprintf("SELECT CRC32C('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'computes crc32c of text field' => \sprintf('SELECT CRC32C(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
