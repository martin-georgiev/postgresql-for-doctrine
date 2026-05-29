<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Every;

class EveryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EVERY' => Every::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'aggregates boolean field' => 'SELECT every(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'aggregates boolean field' => \sprintf('SELECT EVERY(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
