<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateSeries;
use PHPUnit\Framework\Attributes\Test;

class GenerateSeriesTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GENERATE_SERIES' => GenerateSeries::class,
        ];
    }

    #[Test]
    public function can_generate_series_between_two_dates(): void
    {
        $dql = "SELECT GENERATE_SERIES(t.date1, t.date2, '1 day') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertCount(2, $result);

        $dates = [];
        foreach ($result as $row) {
            $this->assertIsString($row['result']);
            $dates[] = \substr($row['result'], 0, 10);
        }

        $this->assertContains('2023-06-15', $dates);
        $this->assertContains('2023-06-16', $dates);
    }

    #[Test]
    public function can_generate_series_between_two_timestamps(): void
    {
        $dql = "SELECT GENERATE_SERIES(t.datetime1, t.datetime2, '12 hours') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertCount(3, $result);
    }
}
