<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions;
use PHPUnit\Framework\Attributes\Test;

final class ArrayNumberOfDimensionsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_NUMBER_OF_DIMENSIONS' => ArrayNumberOfDimensions::class,
        ];
    }

    #[Test]
    public function returns_the_number_of_dimensions_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_NUMBER_OF_DIMENSIONS(ARR('apple', 'banana')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_number_of_dimensions_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_NUMBER_OF_DIMENSIONS(t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
