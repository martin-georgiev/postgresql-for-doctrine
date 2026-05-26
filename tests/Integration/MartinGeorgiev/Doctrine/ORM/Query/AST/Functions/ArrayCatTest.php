<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat;
use PHPUnit\Framework\Attributes\Test;

final class ArrayCatTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_CAT' => ArrayCat::class,
        ];
    }

    #[Test]
    public function concatenates_text_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(:array1, :array2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'array1' => ['apple', 'banana'],
            'array2' => ['orange', 'kiwi'],
        ]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'banana', 'orange', 'kiwi'], $actual);
    }

    #[Test]
    public function concatenates_integer_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(:array1, :array2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'array1' => [1, 2],
            'array2' => [3, 4],
        ]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 2, 3, 4], $actual);
    }

    #[Test]
    public function concatenates_boolean_arrays(): void
    {
        $dql = 'SELECT ARRAY_CAT(:array1, :array2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'array1' => [true, false],
            'array2' => [true, true],
        ]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, false, true, true], $actual);
    }

    #[Test]
    public function concatenates_array_columns(): void
    {
        $dql = 'SELECT ARRAY_CAT(t.textArray, t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'banana', 'orange', 'apple', 'banana', 'orange'], $actual);
    }
}
