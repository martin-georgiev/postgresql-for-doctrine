<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTime;

class MakeTimeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_TIME' => MakeTime::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates time from components' => 'SELECT make_time(10, 30, 0) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates time from components' => \sprintf('SELECT MAKE_TIME(10, 30, 0) FROM %s e', ContainsDates::class),
        ];
    }
}
