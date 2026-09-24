<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract;
use PHPUnit\Framework\Attributes\Test;

final class DateSubtractTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_SUBTRACT' => DateSubtract::class,
        ];
    }

    #[Test]
    public function returns_the_shifted_timestamp_from_an_entity_field(): void
    {
        $dql = "SELECT DATE_SUBTRACT(t.datetimetz1, '1 day') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023-06-14 10:30:00+00', $result[0]['result']);
    }

    #[Test]
    public function returns_the_shifted_timestamp_with_a_time_zone(): void
    {
        $dql = "SELECT DATE_SUBTRACT(t.datetimetz1, '1 day', 'UTC') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023-06-14 10:30:00+00', $result[0]['result']);
    }
}
