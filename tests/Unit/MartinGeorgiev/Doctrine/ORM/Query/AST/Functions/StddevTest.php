<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Stddev;

class StddevTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STDDEV' => Stddev::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes sample standard deviation of integer field' => 'SELECT stddev(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes sample standard deviation of integer field' => \sprintf('SELECT STDDEV(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
