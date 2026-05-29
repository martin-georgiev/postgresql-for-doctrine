<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Variance;

class VarianceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'VARIANCE' => Variance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes sample variance of integer field' => 'SELECT variance(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes sample variance of integer field' => \sprintf('SELECT VARIANCE(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
