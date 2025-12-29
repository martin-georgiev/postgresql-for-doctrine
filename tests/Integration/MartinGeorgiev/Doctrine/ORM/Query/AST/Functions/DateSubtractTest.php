<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract;
use PHPUnit\Framework\Attributes\Test;

class DateSubtractTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_SUBTRACT' => DateSubtract::class,
        ];
    }

    #[Test]
    public function can_subtract_interval_from_timestamp(): void
    {
        $dql = "SELECT DATE_SUBTRACT(t.datetimetz1, '1 day') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertStringContainsString('2023-06-14', $result[0]['result']);
    }

    #[Test]
    public function can_subtract_hours_from_timestamp(): void
    {
        $dql = "SELECT DATE_SUBTRACT(t.datetimetz1, '2 hours') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertStringContainsString('08:30:00', $result[0]['result']);
    }

    #[Test]
    public function can_subtract_interval_with_timezone(): void
    {
        $dql = "SELECT DATE_SUBTRACT(t.datetimetz1, '1 day', 'UTC') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertStringContainsString('2023-06-14', $result[0]['result']);
    }
}
