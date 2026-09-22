<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions;
use PHPUnit\Framework\Attributes\Test;

final class ArrayDimensionsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_DIMENSIONS' => ArrayDimensions::class,
        ];
    }

    #[Test]
    public function returns_the_array_dimensions_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_DIMENSIONS(ARR('apple', 'banana')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1:2]', $result[0]['result']);
    }

    #[Test]
    public function returns_the_array_dimensions_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1:3]', $result[0]['result']);
    }
}
