<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToOct;

final class ToOctTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_OCT' => ToOct::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'writes a literal in octal' => 'SELECT to_oct(255) AS sclr_0 FROM ContainsIntegers c0_',
            'writes a field in octal' => 'SELECT to_oct(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'writes a literal in octal' => \sprintf('SELECT TO_OCT(255) FROM %s e', ContainsIntegers::class),
            'writes a field in octal' => \sprintf('SELECT TO_OCT(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
