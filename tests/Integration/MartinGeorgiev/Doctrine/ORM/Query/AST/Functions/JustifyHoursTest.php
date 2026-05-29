<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyHours;
use PHPUnit\Framework\Attributes\Test;

class JustifyHoursTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JUSTIFY_HOURS' => JustifyHours::class,
        ];
    }

    #[Test]
    public function converts_hours_from_literal_exceeding_threshold_to_days(): void
    {
        $dql = "SELECT JUSTIFY_HOURS('27 hours') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1 day 03:00:00', $result[0]['result']);
    }

    #[Test]
    public function returns_interval_unchanged_when_below_threshold(): void
    {
        $dql = 'SELECT JUSTIFY_HOURS(t.dateinterval1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('15:02:12', $result[0]['result']);
    }
}
