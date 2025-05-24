<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;

class ArrayReplaceTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_REPLACE' => ArrayReplace::class];
    }

    public function test_array_replace_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.textArray, \'banana\', \'mango\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'mango', 'orange'], $actual);
    }

    public function test_array_replace_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.integerArray, 2, 5) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 5, 3], $actual);
    }

    public function test_array_replace_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.boolArray, false, true) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, true, true], $actual);
    }

    public function test_array_replace_with_not_found_element(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.textArray, \'mango\', \'kiwi\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'banana', 'orange'], $actual);
    }
}
