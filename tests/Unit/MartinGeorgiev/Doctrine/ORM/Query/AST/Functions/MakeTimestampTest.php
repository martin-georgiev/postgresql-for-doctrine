<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamp;

class MakeTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_TIMESTAMP' => MakeTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates timestamp from components' => 'SELECT make_timestamp(2023, 6, 15, 10, 30, 0) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates timestamp from components' => \sprintf('SELECT MAKE_TIMESTAMP(2023, 6, 15, 10, 30, 0) FROM %s e', ContainsDates::class),
        ];
    }
}

