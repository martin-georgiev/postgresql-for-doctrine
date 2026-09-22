<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySort;
use PHPUnit\Framework\Attributes\Test;

final class ArraySortTest extends ArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'array_sort function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_SORT' => ArraySort::class,
        ];
    }

    #[Test]
    public function returns_the_sorted_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_SORT(ARR('orange', 'apple', 'banana')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'banana', 'orange'], $actual);
    }

    #[Test]
    public function returns_the_sorted_array_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_SORT(t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['banana', 'kiwi', 'mango', 'orange'], $actual);
    }
}
