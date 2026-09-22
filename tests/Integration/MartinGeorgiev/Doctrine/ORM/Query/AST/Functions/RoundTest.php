<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round;
use PHPUnit\Framework\Attributes\Test;

final class RoundTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ROUND' => Round::class,
        ];
    }

    #[Test]
    public function returns_the_rounded_value_from_a_literal(): void
    {
        $dql = 'SELECT ROUND(3.14159) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }

    #[Test]
    public function returns_the_rounded_value_with_a_precision_argument(): void
    {
        $dql = 'SELECT ROUND(3.14159, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.14, $result[0]['result']);
    }

    #[Test]
    public function returns_the_rounded_value_from_an_entity_field(): void
    {
        $dql = 'SELECT ROUND(t.decimal1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(11, $result[0]['result']);
    }

    #[Test]
    public function returns_the_rounded_value_from_an_arithmetic_expression(): void
    {
        $dql = 'SELECT ROUND(100 * t.integer1 / t.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(50, $result[0]['result']);
    }
}
