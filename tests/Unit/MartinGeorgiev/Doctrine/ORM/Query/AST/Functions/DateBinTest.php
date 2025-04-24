<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'bins by 1 day' => "SELECT date_bin('1 day', c0_.datetime1, '2001-02-16 00:00:00') AS sclr_0 FROM ContainsDates c0_",
            'bins with native function as parameter' => "SELECT date_bin('1 hour', CURRENT_TIMESTAMP, '2001-02-16 00:00:00') AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'bins by 15 minutes' => \sprintf("SELECT DATE_BIN('15 minutes', e.datetime1, '2001-02-16 20:05:00') FROM %s e", ContainsDates::class),
            'bins by 1 day' => \sprintf("SELECT DATE_BIN('1 day', e.datetime1, '2001-02-16 00:00:00') FROM %s e", ContainsDates::class),
            'bins with native function as parameter' => \sprintf("SELECT DATE_BIN('1 hour', CURRENT_TIMESTAMP(), '2001-02-16 00:00:00') FROM %s e", ContainsDates::class),
        ];
    }
}
