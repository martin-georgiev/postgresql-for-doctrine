<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange;

class TstzrangeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TSTZRANGE' => Tstzrange::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT tstzrange(c0_.datetimetz1, c0_.datetimetz2) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TSTZRANGE(e.datetimetz1, e.datetimetz2) FROM %s e', ContainsDates::class),
        ];
    }
}
