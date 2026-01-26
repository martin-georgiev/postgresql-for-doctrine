<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamptz;

class MakeTimestamptzTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_TIMESTAMPTZ' => MakeTimestamptz::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates timestamptz without timezone' => 'SELECT make_timestamptz(2023, 6, 15, 10, 30, 0) AS sclr_0 FROM ContainsDates c0_',
            'creates timestamptz with timezone' => "SELECT make_timestamptz(2023, 6, 15, 10, 30, 0, 'UTC') AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates timestamptz without timezone' => \sprintf('SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0) FROM %s e', ContainsDates::class),
            'creates timestamptz with timezone' => \sprintf("SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0, 'UTC') FROM %s e", ContainsDates::class),
        ];
    }
}

