<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round;

class RoundTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ROUND' => Round::class];
    }

    public function test_round_with_positive_number(): void
    {
        $dql = 'SELECT ROUND(:number) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => 3.14159]);
        $this->assertEquals(3, $result[0]['result']);
    }

    public function test_round_with_negative_number(): void
    {
        $dql = 'SELECT ROUND(:number) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['number' => -3.14159]);
        $this->assertEquals(-3, $result[0]['result']);
    }

    public function test_round_with_precision(): void
    {
        $dql = 'SELECT ROUND(:number, :precision) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'number' => 3.14159,
            'precision' => 2,
        ]);
        $this->assertEquals(3.14, $result[0]['result']);
    }

    public function test_round_with_negative_precision(): void
    {
        $dql = 'SELECT ROUND(:number, :precision) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'number' => 314.159,
            'precision' => -2,
        ]);
        $this->assertEquals(300, $result[0]['result']);
    }

    public function test_round_with_column_value(): void
    {
        $dql = 'SELECT ROUND(t.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(11, $result[0]['result']);
    }
}
