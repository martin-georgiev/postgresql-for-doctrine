<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyDays;
use PHPUnit\Framework\Attributes\Test;

class JustifyDaysTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JUSTIFY_DAYS' => JustifyDays::class,
        ];
    }

    #[Test]
    public function converts_days_from_literal_exceeding_threshold_to_months(): void
    {
        $dql = "SELECT JUSTIFY_DAYS('35 days') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1 mon 5 days', $result[0]['result']);
    }

    #[Test]
    public function returns_interval_unchanged_when_below_threshold(): void
    {
        $dql = 'SELECT JUSTIFY_DAYS(t.dateinterval1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('15:02:12', $result[0]['result']);
    }
}
