<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc;
use PHPUnit\Framework\Attributes\Test;

final class TruncTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRUNC' => Trunc::class,
        ];
    }

    #[Test]
    public function returns_the_truncated_value_from_a_literal(): void
    {
        $dql = 'SELECT TRUNC(42.8) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(42, $result[0]['result']);
    }

    #[Test]
    public function returns_the_truncated_value_with_a_precision(): void
    {
        $dql = 'SELECT TRUNC(42.4382, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(42.43, $result[0]['result']);
    }

    #[Test]
    public function returns_the_truncated_value_from_an_entity_field(): void
    {
        $dql = 'SELECT TRUNC(t.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_truncated_value_from_an_arithmetic_expression(): void
    {
        $dql = 'SELECT TRUNC(t.integer1 * t.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(105, $result[0]['result']);
    }
}
