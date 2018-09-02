<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsSeveralIntegers;

class GreatestTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GREATEST' => Greatest::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT greatest(c0_.integer1,c0_.integer2,c0_.integer3) AS sclr_0 FROM ContainsSeveralIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf('SELECT GREATEST(e.integer1, e.integer2, e.integer3) FROM %s e', ContainsSeveralIntegers::class),
        ];
    }
}
