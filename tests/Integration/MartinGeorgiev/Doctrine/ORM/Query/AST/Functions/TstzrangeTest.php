<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange;
use PHPUnit\Framework\Attributes\Test;

final class TstzrangeTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TSTZRANGE' => Tstzrange::class,
        ];
    }

    #[Test]
    public function creates_a_range_from_entity_fields(): void
    {
        $dql = 'SELECT TSTZRANGE(t.datetimetz1, t.datetimetz2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('["2023-06-15 10:30:00+00","2023-06-16 11:45:00+00")', $result[0]['result']);
    }

    #[Test]
    public function creates_a_range_from_literals_with_an_explicit_bounds_argument(): void
    {
        $dql = "SELECT TSTZRANGE('2023-06-15 10:30:00+00', '2023-06-16 11:45:00+00', '(]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('("2023-06-15 10:30:00+00","2023-06-16 11:45:00+00"]', $result[0]['result']);
    }
}
