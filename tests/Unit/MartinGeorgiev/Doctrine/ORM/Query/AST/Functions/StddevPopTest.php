<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StddevPop;

class StddevPopTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STDDEV_POP' => StddevPop::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes population standard deviation of integer field' => 'SELECT stddev_pop(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes population standard deviation of integer field' => \sprintf('SELECT STDDEV_POP(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
