<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;
use PHPUnit\Framework\Attributes\Test;

class ArrayCardinalityTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_CARDINALITY' => ArrayCardinality::class,
        ];
    }

    #[Test]
    public function array_cardinality_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_CARDINALITY(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function array_cardinality_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_CARDINALITY(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function array_cardinality_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_CARDINALITY(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
