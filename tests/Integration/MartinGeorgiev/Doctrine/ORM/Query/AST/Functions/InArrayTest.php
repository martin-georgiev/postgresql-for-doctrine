<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray;
use PHPUnit\Framework\Attributes\Test;

final class InArrayTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'IN_ARRAY' => InArray::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_value_is_in_an_entity_field(): void
    {
        $dql = 'SELECT IN_ARRAY(:value, t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => 'banana']);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_the_value_is_in_an_array_literal(): void
    {
        $dql = "SELECT IN_ARRAY(:value, ARR('apple', 'banana')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql, ['value' => 'banana']);
        $this->assertTrue($result[0]['result']);
    }
}
