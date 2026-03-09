<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AtTimeZone;
use PHPUnit\Framework\Attributes\Test;

class AtTimeZoneTest extends TextTestCase
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
        $dql = "SELECT AT_TIME_ZONE('2001-02-16 20:38:40+00', 'America/New_York') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2001-02-16', $result[0]['result']);
    }

    #[Test]
    public function converts_timestamp_to_utc(): void
    {
        $dql = "SELECT AT_TIME_ZONE('2024-01-15 12:00:00', 'UTC') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2024-01-15', $result[0]['result']);
    }
}
