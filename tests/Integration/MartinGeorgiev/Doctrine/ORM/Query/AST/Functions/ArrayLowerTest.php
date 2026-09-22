<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLower;
use PHPUnit\Framework\Attributes\Test;

final class ArrayLowerTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_LOWER' => ArrayLower::class,
        ];
    }

    #[Test]
    public function returns_the_lower_bound_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_LOWER(ARR('apple', 'banana'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_lower_bound_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_LOWER(t.textArray, 1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
