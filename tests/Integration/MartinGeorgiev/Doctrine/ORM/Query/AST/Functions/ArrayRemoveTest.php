<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;

class ArrayRemoveTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_REMOVE' => ArrayRemove::class];
    }

    public function test_array_remove_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.textArray, \'banana\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'orange'], $actual);
    }

    public function test_array_remove_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.integerArray, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 3], $actual);
    }

    public function test_array_remove_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.boolArray, false) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, true], $actual);
    }

    public function test_array_remove_with_not_found_element(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.textArray, \'mango\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'banana', 'orange'], $actual);
    }
}
