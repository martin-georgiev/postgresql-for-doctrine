<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReverse;
use PHPUnit\Framework\Attributes\Test;

class ArrayReverseTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REVERSE' => ArrayReverse::class,
        ];
    }

    #[Test]
    public function can_reverse_text_array(): void
    {
        $dql = 'SELECT ARRAY_REVERSE(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['orange', 'banana', 'apple'], $actual);
    }

    #[Test]
    public function can_reverse_integer_array(): void
    {
        $dql = 'SELECT ARRAY_REVERSE(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([3, 2, 1], $actual);
    }

    #[Test]
    public function can_reverse_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_REVERSE(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, false, true], $actual);
    }
}
