<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least;
use PHPUnit\Framework\Attributes\Test;

class LeastTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEAST' => Least::class,
        ];
    }

    #[Test]
    public function can_find_least_of_two_values(): void
    {
        $dql = 'SELECT LEAST(t.id, 100) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_find_least_of_multiple_values(): void
    {
        $dql = 'SELECT LEAST(5, 1, 3, 2, 4) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_compare_column_values(): void
    {
        $dql = 'SELECT LEAST(t.id, 0) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }
}
