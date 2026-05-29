<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Isfinite;

class IsfiniteTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ISFINITE' => Isfinite::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'tests if a date is finite' => 'SELECT isfinite(c0_.date1) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'tests if a date is finite' => \sprintf('SELECT ISFINITE(e.date1) FROM %s e', ContainsDates::class),
        ];
    }
}
