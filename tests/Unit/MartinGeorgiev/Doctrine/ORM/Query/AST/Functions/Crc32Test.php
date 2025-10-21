<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32;

class Crc32Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CRC32' => Crc32::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes crc32 of a string' => "SELECT crc32('Hello Doctrine'::bytea) AS sclr_0 FROM ContainsTexts c0_",
            'computes crc32 of text field' => 'SELECT crc32(c0_.text1::bytea) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes crc32 of a string' => \sprintf("SELECT CRC32('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'computes crc32 of text field' => \sprintf('SELECT CRC32(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
