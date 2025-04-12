<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin;

class DateBinTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_BIN' => DateBin::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'bins by 15 minutes' => "SELECT date_bin('15 minutes', c0_.datetime1, '2001-02-16 20:05:00') AS sclr_0 FROM ContainsDates c0_",
            'bins by 1 hour' => "SELECT date_bin('1 hour', c0_.datetime1, '2001-02-16 00:00:00') AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'bins by 15 minutes' => \sprintf("SELECT DATE_BIN('15 minutes', e.datetime1, '2001-02-16 20:05:00') FROM %s e", ContainsDates::class),
            'bins by 1 hour' => \sprintf("SELECT DATE_BIN('1 hour', e.datetime1, '2001-02-16 00:00:00') FROM %s e", ContainsDates::class),
        ];
    }
}
