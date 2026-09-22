<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition;
use PHPUnit\Framework\Attributes\Test;

final class ArrayPositionTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_POSITION' => ArrayPosition::class,
        ];
    }

    #[Test]
    public function returns_the_element_position_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_POSITION(ARR('apple', 'banana'), 'banana') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function returns_the_element_position_from_an_entity_field(): void
    {
        $dql = "SELECT ARRAY_POSITION(t.textArray, 'orange') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
