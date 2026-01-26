<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeDate;
use PHPUnit\Framework\Attributes\Test;

class MakeDateTest extends DateMakingTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
            'DATE_PART' => DatePart::class,
            'MAKE_DATE' => MakeDate::class,
        ];
    }

    #[Test]
    public function can_create_date_from_components(): void
    {
        $dql = "SELECT MAKE_DATE(
                    CAST(DATE_PART('year', t.date1) AS INTEGER),
                    CAST(DATE_PART('month', t.date1) AS INTEGER),
                    CAST(DATE_PART('day', t.date1) AS INTEGER)
                ) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('2023-06-15', $result[0]['result']);
    }

    #[Test]
    public function can_create_date_from_entity_fields(): void
    {
        $dql = 'SELECT MAKE_DATE(2023, n.integer1, n.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('2023-10-20', $result[0]['result']);
    }
}
