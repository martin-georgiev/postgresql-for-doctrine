<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Corr;

class CorrTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CORR' => Corr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes correlation coefficient of two numeric fields' => 'SELECT corr(c0_.decimal1, c0_.decimal2) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes correlation coefficient of two numeric fields' => \sprintf('SELECT CORR(e.decimal1, e.decimal2) FROM %s e', ContainsNumerics::class),
        ];
    }
}
