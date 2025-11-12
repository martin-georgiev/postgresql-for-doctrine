<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc;
use PHPUnit\Framework\Attributes\Test;

class TruncTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRUNC' => Trunc::class,
        ];
    }

    #[Test]
    public function can_trunc_positive_number(): void
    {
        $dql = 'SELECT TRUNC(42.8) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(42, $result[0]['result']);
    }

    #[Test]
    public function can_trunc_negative_number(): void
    {
        $dql = 'SELECT TRUNC(-42.8) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(-42, $result[0]['result']);
    }

    #[Test]
    public function can_trunc_with_precision(): void
    {
        $dql = 'SELECT TRUNC(42.4382, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(42.43, $result[0]['result']);
    }

    #[Test]
    public function can_trunc_with_negative_precision(): void
    {
        $dql = 'SELECT TRUNC(1234.56, -2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1200, $result[0]['result']);
    }

    #[Test]
    public function can_trunc_column_value(): void
    {
        $dql = 'SELECT TRUNC(t.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }

    #[Test]
    public function can_trunc_arithmetic_expression(): void
    {
        $dql = 'SELECT TRUNC(t.integer1 * t.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(105, $result[0]['result']);
    }

    #[Test]
    public function can_trunc_arithmetic_expression_with_precision(): void
    {
        $dql = 'SELECT TRUNC(100 * t.integer1 / t.integer2, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(50.00, $result[0]['result']);
    }

    #[Test]
    public function can_trunc_parenthesized_arithmetic_expression(): void
    {
        $dql = 'SELECT TRUNC((t.integer1 + t.integer2) * t.decimal1, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(315.0, $result[0]['result']);
    }
}
