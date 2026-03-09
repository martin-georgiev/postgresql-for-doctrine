<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AtTimeZone;
use PHPUnit\Framework\Attributes\Test;

class AtTimeZoneTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'AT_TIME_ZONE' => AtTimeZone::class,
        ];
    }

    #[Test]
    public function converts_timestamptz_to_local_timezone(): void
    {
        $dql = "SELECT AT_TIME_ZONE(t.datetimetz1, 'America/New_York') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        // datetimetz1 = '2023-06-15 10:30:00+00' (UTC); New York is UTC-4 in June → 06:30:00
        $this->assertSame('2023-06-15 06:30:00', $result[0]['result']);
    }

    #[Test]
    public function converts_timestamp_to_timestamptz(): void
    {
        $dql = "SELECT AT_TIME_ZONE(t.datetime1, 'UTC') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        // datetime1 = '2023-06-15 10:30:00' (no tz); interpreting as UTC → timestamptz '2023-06-15 10:30:00+00'
        $this->assertSame('2023-06-15 10:30:00+00', $result[0]['result']);
    }
}
