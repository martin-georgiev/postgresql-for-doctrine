<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor;

class FloorTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FLOOR' => Floor::class,
        ];
    }

    public function test_floor_with_positive_decimal(): void
    {
        $dql = 'SELECT FLOOR(:number) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => 10.5]);
        $this->assertEquals(10, $result[0]['result']);
    }

    public function test_floor_with_negative_decimal(): void
    {
        $dql = 'SELECT FLOOR(:number) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => -10.5]);
        $this->assertEquals(-11, $result[0]['result']);
    }

    public function test_floor_with_column_value(): void
    {
        $dql = 'SELECT FLOOR(t.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }
}
