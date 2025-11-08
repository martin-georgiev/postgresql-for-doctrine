<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt;
use PHPUnit\Framework\Attributes\Test;

class CbrtTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CBRT' => Cbrt::class,
        ];
    }

    #[Test]
    public function can_calculate_cube_root_of_perfect_cube(): void
    {
        $dql = 'SELECT CBRT(27.0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_calculate_cube_root_of_non_perfect_cube(): void
    {
        $dql = 'SELECT CBRT(10.0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.1544, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_calculate_cube_root_of_negative_number(): void
    {
        $dql = 'SELECT CBRT((-27.0)) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(-3.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_calculate_cube_root_of_column_value(): void
    {
        $dql = 'SELECT CBRT(n.decimal1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.1897595699439445, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_calculate_cube_root_of_arithmetic_expression(): void
    {
        $dql = 'SELECT CBRT(n.integer1 + n.integer2 - 3) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.0, $result[0]['result'], 0.0001);
    }
}
