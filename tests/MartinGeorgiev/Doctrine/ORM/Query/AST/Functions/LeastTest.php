<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsSeveralIntegers;

class LeastTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEAST' => Least::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT least(c0_.integer1,c0_.integer2,c0_.integer3) AS sclr_0 FROM ContainsSeveralIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT LEAST(e.integer1, e.integer2, e.integer3) FROM %s e', ContainsSeveralIntegers::class),
        ];
    }
}
