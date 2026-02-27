<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;
use PHPUnit\Framework\Attributes\Test;

class ArrTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'CONTAINS' => Contains::class,
        ];
    }

    #[Test]
    public function can_create_array_from_values(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE CONTAINS(t.textArray, ARR('apple', 'banana')) = true 
                AND t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_create_single_element_array(): void
    {
        $dql = "SELECT t.id as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE CONTAINS(t.textArray, ARR('apple')) = true";
        $result = $this->executeDqlQuery($dql);
        $this->assertGreaterThan(0, \count($result));
    }
}
