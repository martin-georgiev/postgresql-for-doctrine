<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;

class ArrayAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_AGG' => ArrayAgg::class];
    }

    public function test_array_agg_with_text_values(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                ORDER BY t.id';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertCount(3, $actual);
        $this->assertContains('apple', $actual);
        $this->assertContains('banana', $actual);
        $this->assertContains('orange', $actual);
    }

    public function test_array_agg_with_integer_values(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                ORDER BY t.id';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertCount(3, $actual);
        $this->assertContains(1, $actual);
        $this->assertContains(2, $actual);
        $this->assertContains(3, $actual);
    }

    public function test_array_agg_with_distinct(): void
    {
        $dql = 'SELECT ARRAY_AGG(DISTINCT t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertCount(3, $actual);
        $this->assertEqualsCanonicalizing([1, 2, 3], $actual);
    }
}
