<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToBin;

final class ToBinTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_BIN' => ToBin::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'writes a literal in binary' => 'SELECT to_bin(255) AS sclr_0 FROM ContainsIntegers c0_',
            'writes a field in binary' => 'SELECT to_bin(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'writes a literal in binary' => \sprintf('SELECT TO_BIN(255) FROM %s e', ContainsIntegers::class),
            'writes a field in binary' => \sprintf('SELECT TO_BIN(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
