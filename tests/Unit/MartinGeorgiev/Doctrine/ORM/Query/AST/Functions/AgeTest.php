<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Age;

class AgeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'AGE' => Age::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates age between timestamps' => 'SELECT age(c0_.datetime2, c0_.datetime1) AS sclr_0 FROM ContainsDates c0_',
            'calculates age from current timestamp' => 'SELECT age(c0_.datetime1) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates age between timestamps' => \sprintf('SELECT AGE(e.datetime2, e.datetime1) FROM %s e', ContainsDates::class),
            'calculates age from current timestamp' => \sprintf('SELECT AGE(e.datetime1) FROM %s e', ContainsDates::class),
        ];
    }
}
