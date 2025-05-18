<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue;

class AnyValueTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ANY_VALUE' => AnyValue::class];
    }

    public function test_any_value_with_text_array(): void
    {
        $dql = 'SELECT ANY_VALUE(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertContains($actual, ['apple', 'banana', 'orange']);
    }

    public function test_any_value_with_integer_array(): void
    {
        $dql = 'SELECT ANY_VALUE(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertContains($result[0]['result'], [1, 2, 3]);
    }

    public function test_any_value_with_boolean_array(): void
    {
        $dql = 'SELECT ANY_VALUE(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertContains($result[0]['result'], [true, false]);
    }
}
