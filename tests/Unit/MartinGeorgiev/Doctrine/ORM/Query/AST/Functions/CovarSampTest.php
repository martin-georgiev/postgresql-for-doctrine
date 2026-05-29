<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarSamp;

class CovarSampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COVAR_SAMP' => CovarSamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes sample covariance of two numeric fields' => 'SELECT covar_samp(c0_.decimal1, c0_.decimal2) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes sample covariance of two numeric fields' => \sprintf('SELECT COVAR_SAMP(e.decimal1, e.decimal2) FROM %s e', ContainsNumerics::class),
        ];
    }
}
