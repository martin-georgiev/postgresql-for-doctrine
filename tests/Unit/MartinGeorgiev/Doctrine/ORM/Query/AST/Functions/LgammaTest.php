<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lgamma;

class LgammaTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LGAMMA' => Lgamma::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes lgamma of number' => 'SELECT lgamma(5) AS sclr_0 FROM ContainsNumerics c0_',
            'computes lgamma of field' => 'SELECT lgamma(c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes lgamma of number' => \sprintf('SELECT LGAMMA(5) FROM %s e', ContainsNumerics::class),
            'computes lgamma of field' => \sprintf('SELECT LGAMMA(e.integer1) FROM %s e', ContainsNumerics::class),
        ];
    }
}
