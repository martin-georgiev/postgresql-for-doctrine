<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin;
use PHPUnit\Framework\Attributes\Test;

class DateBinTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_BIN' => DateBin::class,
        ];
    }

    #[Test]
    public function can_bin_timestamp_to_15_minute_intervals(): void
    {
        $dql = "SELECT DATE_BIN('15 minutes', t.datetime1, '2023-06-15 00:00:00') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertStringContainsString('2023-06-15 10:30:00', $result[0]['result']);
    }

    #[Test]
    public function can_bin_timestamp_to_hourly_intervals(): void
    {
        $dql = "SELECT DATE_BIN('1 hour', t.datetime1, '2023-06-15 00:00:00') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertStringContainsString('2023-06-15 10:00:00', $result[0]['result']);
    }

    #[Test]
    public function can_bin_timestamp_to_daily_intervals(): void
    {
        $dql = "SELECT DATE_BIN('1 day', t.datetime1, '2023-06-01 00:00:00') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertStringContainsString('2023-06-15 00:00:00', $result[0]['result']);
    }
}
