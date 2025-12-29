<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest;
use PHPUnit\Framework\Attributes\Test;

class GreatestTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GREATEST' => Greatest::class,
        ];
    }

    #[Test]
    public function can_find_greatest_of_two_values(): void
    {
        $dql = 'SELECT GREATEST(t.id, 0) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_find_greatest_of_multiple_values(): void
    {
        $dql = 'SELECT GREATEST(1, 5, 3, 2, 4) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function can_compare_column_values(): void
    {
        $dql = 'SELECT GREATEST(t.id, 100) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(100, $result[0]['result']);
    }
}
