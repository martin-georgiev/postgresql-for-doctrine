<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps;
use PHPUnit\Framework\Attributes\Test;

final class DateOverlapsTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_OVERLAPS' => DateOverlaps::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_entity_field_range_overlaps(): void
    {
        $dql = "SELECT DATE_OVERLAPS(t.date1, t.date2, '2023-06-14', '2023-06-17') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
