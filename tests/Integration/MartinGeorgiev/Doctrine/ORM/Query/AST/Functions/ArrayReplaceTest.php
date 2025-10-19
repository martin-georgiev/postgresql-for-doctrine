<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;
use PHPUnit\Framework\Attributes\Test;

class ArrayReplaceTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REPLACE' => ArrayReplace::class,
        ];
    }

    #[Test]
    public function can_replace_text_elements(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.textArray, \'banana\', \'mango\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'mango', 'orange'], $actual);
    }

    #[Test]
    public function can_replace_integer_elements(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.integerArray, 2, 5) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 5, 3], $actual);
    }

    #[Test]
    public function can_replace_boolean_elements(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.boolArray, false, true) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, true, true], $actual);
    }

    #[Test]
    public function leaves_array_unchanged_when_element_not_found(): void
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
