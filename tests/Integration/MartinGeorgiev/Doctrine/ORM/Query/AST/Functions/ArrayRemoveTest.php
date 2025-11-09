<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;
use PHPUnit\Framework\Attributes\Test;

class ArrayRemoveTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REMOVE' => ArrayRemove::class,
        ];
    }

    #[Test]
    public function can_remove_text_elements(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.textArray, \'banana\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'orange'], $actual);
    }

    #[Test]
    public function can_remove_integer_elements(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.integerArray, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 3], $actual);
    }

    #[Test]
    public function can_remove_boolean_elements(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.boolArray, false) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, true], $actual);
    }

    #[Test]
    public function leaves_array_unchanged_when_element_not_found(): void
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
