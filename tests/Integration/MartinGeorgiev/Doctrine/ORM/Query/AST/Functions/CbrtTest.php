<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt;

class CbrtTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CBRT' => Cbrt::class,
        ];
    }

    public function test_cbrt_with_perfect_cube(): void
    {
        $dql = 'SELECT CBRT(27.0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.0, $result[0]['result'], 0.0001);
    }

    public function test_cbrt_with_non_perfect_cube(): void
    {
        $dql = 'SELECT CBRT(10.0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.1544, $result[0]['result'], 0.0001);
    }

    public function test_cbrt_with_negative_number(): void
    {
        $dql = 'SELECT CBRT((-27.0)) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(-3.0, $result[0]['result'], 0.0001);
    }

    public function test_cbrt_with_column_value(): void
    {
        $dql = 'SELECT CBRT(n.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.1897595699439445, $result[0]['result'], 0.0001);
    }
}
