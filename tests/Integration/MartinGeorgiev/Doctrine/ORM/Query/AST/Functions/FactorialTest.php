<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Factorial;
use PHPUnit\Framework\Attributes\Test;

final class FactorialTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FACTORIAL' => Factorial::class,
        ];
    }

    #[Test]
    public function returns_the_factorial_from_a_numeric_literal(): void
    {
        $dql = 'SELECT FACTORIAL(5) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(120, $result[0]['result']);
    }

    #[Test]
    public function returns_the_factorial_from_an_entity_field(): void
    {
        $dql = 'SELECT FACTORIAL(n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3628800, $result[0]['result']);
    }
}
