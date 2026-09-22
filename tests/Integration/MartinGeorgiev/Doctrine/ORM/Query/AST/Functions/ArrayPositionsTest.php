<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions;
use PHPUnit\Framework\Attributes\Test;

final class ArrayPositionsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_POSITIONS' => ArrayPositions::class,
        ];
    }

    #[Test]
    public function returns_the_element_positions_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_POSITIONS(ARR('apple', 'banana', 'apple'), 'apple') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame([1, 3], $actual);
    }

    #[Test]
    public function returns_the_element_positions_from_an_entity_field(): void
    {
        $dql = "SELECT ARRAY_POSITIONS(t.textArray, 'kiwi') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame([3], $actual);
    }
}
