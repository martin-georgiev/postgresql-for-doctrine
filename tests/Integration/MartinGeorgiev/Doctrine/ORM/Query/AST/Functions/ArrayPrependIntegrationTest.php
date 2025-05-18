<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend;
use Tests\Integration\MartinGeorgiev\TestCase;

class ArrayPrependIntegrationTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_PREPEND' => ArrayPrepend::class];
    }

    public function test_array_prepend_with_text_array(): void
    {
        $dql = "SELECT ARRAY_PREPEND('orange', t.textArray) as prepended 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['prepended']);
        $this->assertEquals(['orange', 'apple', 'banana', 'orange'], $actual);
    }

    public function test_array_prepend_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_PREPEND(3, t.integerArray) as prepended 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['prepended']);
        $this->assertEquals([3, 1, 2, 3], $actual);
    }

    public function test_array_prepend_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_PREPEND(true, t.boolArray) as prepended 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['prepended']);
        $this->assertEquals([true, true, false, true], $actual);
    }
}
