<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileDisc;

final class PercentileDiscTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PERCENTILE_DISC' => PercentileDisc::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'orders ascending by default' => 'SELECT percentile_disc(0.5) WITHIN GROUP (ORDER BY c0_.integer1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'orders descending' => 'SELECT percentile_disc(0.9) WITHIN GROUP (ORDER BY c0_.integer1 DESC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'orders ascending by default' => \sprintf('SELECT PERCENTILE_DISC(0.5 WITHIN GROUP ORDER BY e.integer1) FROM %s e', ContainsNumerics::class),
            'orders descending' => \sprintf('SELECT PERCENTILE_DISC(0.9 WITHIN GROUP ORDER BY e.integer1 DESC) FROM %s e', ContainsNumerics::class),
        ];
    }
}
