<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySort;
use PHPUnit\Framework\Attributes\Test;

class ArraySortTest extends ArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'array_sort function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_SORT' => ArraySort::class,
        ];
    }

    #[Test]
    public function can_sort_text_array(): void
    {
        $dql = 'SELECT ARRAY_SORT(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'banana', 'orange'], $actual);
    }

    #[Test]
    public function can_sort_integer_array(): void
    {
        $dql = 'SELECT ARRAY_SORT(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 2, 3], $actual);
    }

    #[Test]
    public function can_sort_unsorted_array(): void
    {
        $dql = 'SELECT ARRAY_SORT(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'grape'], $actual);
    }
}
