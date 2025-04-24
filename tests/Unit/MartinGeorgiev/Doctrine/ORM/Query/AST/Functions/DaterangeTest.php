<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange;

class DaterangeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATERANGE' => Daterange::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT daterange(c0_.date1, c0_.date2) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DATERANGE(e.date1, e.date2) FROM %s e', ContainsDates::class),
        ];
    }
}
