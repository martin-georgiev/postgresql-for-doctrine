<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat;

class ArrayCatTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_CAT' => ArrayCat::class];
    }

    public function test_array_cat_with_text_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(:array1, :array2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'array1' => ['apple', 'banana'],
            'array2' => ['orange', 'kiwi'],
        ]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals(['apple', 'banana', 'orange', 'kiwi'], $actual);
    }

    public function test_array_cat_with_integer_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(:array1, :array2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'array1' => [1, 2],
            'array2' => [3, 4],
        ]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([1, 2, 3, 4], $actual);
    }

    public function test_array_cat_with_boolean_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(:array1, :array2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'array1' => [true, false],
            'array2' => [true, true],
        ]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([true, false, true, true], $actual);
    }

    public function test_array_cat_with_array_columns(): void
    {
        $dql = 'SELECT ARRAY_CAT(t.textArray, t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals(['apple', 'banana', 'orange', 'apple', 'banana', 'orange'], $actual);
    }
}
