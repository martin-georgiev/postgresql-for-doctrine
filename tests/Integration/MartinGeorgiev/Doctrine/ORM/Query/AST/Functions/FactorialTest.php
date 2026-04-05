<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Factorial;
use PHPUnit\Framework\Attributes\Test;

class FactorialTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FACTORIAL' => Factorial::class,
        ];
    }

    #[Test]
    public function can_calculate_factorial_of_literal(): void
    {
        $dql = 'SELECT FACTORIAL(5) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(120, $result[0]['result']);
    }

    #[Test]
    public function can_calculate_factorial_with_entity_property(): void
    {
        $dql = 'SELECT FACTORIAL(n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3628800, $result[0]['result']);
    }
}
