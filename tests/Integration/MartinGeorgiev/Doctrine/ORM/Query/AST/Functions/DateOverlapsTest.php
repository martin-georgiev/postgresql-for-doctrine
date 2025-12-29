<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps;
use PHPUnit\Framework\Attributes\Test;

class DateOverlapsTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_OVERLAPS' => DateOverlaps::class,
        ];
    }

    #[Test]
    public function can_detect_overlapping_date_ranges(): void
    {
        $dql = "SELECT DATE_OVERLAPS(t.date1, t.date2, '2023-06-14', '2023-06-17') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function can_detect_non_overlapping_date_ranges(): void
    {
        $dql = "SELECT DATE_OVERLAPS(t.date1, t.date2, '2023-07-01', '2023-07-10') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function can_detect_overlapping_datetime_ranges(): void
    {
        $dql = "SELECT DATE_OVERLAPS(t.datetime1, t.datetime2, '2023-06-15 09:00:00', '2023-06-15 12:00:00') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
