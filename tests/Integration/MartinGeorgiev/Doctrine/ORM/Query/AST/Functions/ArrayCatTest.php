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
        $dql = "SELECT ARRAY_CAT(ARRAY['apple', 'banana'], ARRAY['orange', 'kiwi']) as result";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals(['apple', 'banana', 'orange', 'kiwi'], $actual);
    }

    public function test_array_cat_with_integer_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(ARRAY[1, 2], ARRAY[3, 4]) as result';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([1, 2, 3, 4], $actual);
    }

    public function test_array_cat_with_boolean_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(ARRAY[true, false], ARRAY[true, true]) as result';
        $result = $this->executeDqlQuery($dql);
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
