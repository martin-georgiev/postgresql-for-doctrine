<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power;

class PowerTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'POWER' => Power::class,
        ];
    }

    public function test_power_with_integer_exponent(): void
    {
        $dql = 'SELECT POWER(2, 3) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(8, $result[0]['result']);
    }

    public function test_power_with_fractional_exponent(): void
    {
        $dql = 'SELECT POWER(9, 0.5) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.0, $result[0]['result'], 0.0001);
    }

    public function test_power_with_negative_base(): void
    {
        $dql = 'SELECT POWER(-2, 3) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(-8, $result[0]['result']);
    }

    public function test_power_with_negative_exponent(): void
    {
        $dql = 'SELECT POWER(2, -2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.25, $result[0]['result'], 0.0001);
    }

    public function test_power_with_column_values(): void
    {
        $dql = 'SELECT POWER(n.decimal1, n.decimal2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(859766721136081107225.6, $result[0]['result'], 0.0001);
    }
}
