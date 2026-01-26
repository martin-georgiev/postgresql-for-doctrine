<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeDate;

class MakeDateTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_DATE' => MakeDate::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates date from components' => 'SELECT make_date(2023, 6, 15) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates date from components' => \sprintf('SELECT MAKE_DATE(2023, 6, 15) FROM %s e', ContainsDates::class),
        ];
    }
}

