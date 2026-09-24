<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange;
use PHPUnit\Framework\Attributes\Test;

final class DaterangeTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATERANGE' => Daterange::class,
        ];
    }

    #[Test]
    public function creates_a_date_range_from_entity_fields(): void
    {
        $dql = 'SELECT DATERANGE(t.date1, t.date2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[2023-06-15,2023-06-16)', $result[0]['result']);
    }

    #[Test]
    public function creates_a_date_range_with_explicit_bounds(): void
    {
        $dql = "SELECT DATERANGE(t.date1, t.date2, '(]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[2023-06-16,2023-06-17)', $result[0]['result']);
    }
}
