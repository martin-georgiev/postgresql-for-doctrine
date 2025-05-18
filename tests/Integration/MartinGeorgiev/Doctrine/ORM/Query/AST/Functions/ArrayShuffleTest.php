<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle;

class ArrayShuffleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_SHUFFLE' => ArrayShuffle::class];
    }

    public function test_array_shuffle_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_SHUFFLE(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertCount(3, $actual);
        $this->assertEqualsCanonicalizing(['apple', 'banana', 'orange'], $actual);
    }

    public function test_array_shuffle_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_SHUFFLE(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertCount(3, $actual);
        $this->assertEqualsCanonicalizing([1, 2, 3], $actual);
    }

    public function test_array_shuffle_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_SHUFFLE(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertCount(3, $actual);
        $this->assertEqualsCanonicalizing([true, false, true], $actual);
    }
}
