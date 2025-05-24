<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange;

class TsrangeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TSRANGE' => Tsrange::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic range with default bounds' => 'SELECT tsrange(c0_.datetime1, c0_.datetime2) AS sclr_0 FROM ContainsDates c0_',
            'range with explicit bounds' => "SELECT tsrange(c0_.datetime1, c0_.datetime2, '[)') AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic range with default bounds' => \sprintf('SELECT TSRANGE(e.datetime1, e.datetime2) FROM %s e', ContainsDates::class),
            'range with explicit bounds' => \sprintf("SELECT TSRANGE(e.datetime1, e.datetime2, '[)') FROM %s e", ContainsDates::class),
        ];
    }
}
