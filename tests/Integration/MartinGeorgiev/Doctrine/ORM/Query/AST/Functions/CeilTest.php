<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil;

class CeilTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CEIL' => Ceil::class,
        ];
    }

    public function test_ceil_with_positive_decimal(): void
    {
        $dql = 'SELECT CEIL(:number) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => 10.5]);
        $this->assertEquals(11, $result[0]['result']);
    }

    public function test_ceil_with_negative_decimal(): void
    {
        $dql = 'SELECT CEIL(:number) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => -10.5]);
        $this->assertEquals(-10, $result[0]['result']);
    }

    public function test_ceil_with_column_value(): void
    {
        $dql = 'SELECT CEIL(t.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(11, $result[0]['result']);
    }
}
