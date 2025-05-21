<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange;

class DaterangeTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATERANGE' => Daterange::class,
        ];
    }

    public function test_daterange(): void
    {
        $dql = 'SELECT DATERANGE(t.date1, t.date2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[2023-06-15,2023-06-16)', $result[0]['result']);
    }

    public function test_daterange_with_bounds(): void
    {
        $dql = 'SELECT DATERANGE(t.date1, t.date2, "[)") as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result[0]['result']);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[2023-06-15,2023-06-16)', $result[0]['result']);
    }
}
