<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gamma;

class GammaTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GAMMA' => Gamma::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes gamma of number' => 'SELECT gamma(5) AS sclr_0 FROM ContainsNumerics c0_',
            'computes gamma of field' => 'SELECT gamma(c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes gamma of number' => \sprintf('SELECT GAMMA(5) FROM %s e', ContainsNumerics::class),
            'computes gamma of field' => \sprintf('SELECT GAMMA(e.integer1) FROM %s e', ContainsNumerics::class),
        ];
    }
}
