<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd;
use PHPUnit\Framework\Attributes\Test;

class DateAddTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_ADD' => DateAdd::class,
        ];
    }

    #[Test]
    public function can_add_interval_to_timestamp(): void
    {
        $dql = "SELECT DATE_ADD(t.datetimetz1, '1 day') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2023-06-16', $result[0]['result']);
    }

    #[Test]
    public function can_add_hours_to_timestamp(): void
    {
        $dql = "SELECT DATE_ADD(t.datetimetz1, '2 hours') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('12:30:00', $result[0]['result']);
    }

    #[Test]
    public function can_add_interval_with_timezone(): void
    {
        $dql = "SELECT DATE_ADD(t.datetimetz1, '1 day', 'UTC') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2023-06-16', $result[0]['result']);
    }
}
