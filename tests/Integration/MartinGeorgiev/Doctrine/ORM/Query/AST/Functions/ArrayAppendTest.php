<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend;
use PHPUnit\Framework\Attributes\Test;

class ArrayAppendTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_APPEND' => ArrayAppend::class];
    }

    #[Test]
    public function array_append_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_APPEND(t.textArray, \'orange\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['apple', 'banana', 'orange', 'orange'], $actual);
    }

    #[Test]
    public function array_append_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_APPEND(t.integerArray, 3) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([1, 2, 3, 3], $actual);
    }

    #[Test]
    public function array_append_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_APPEND(t.boolArray, true) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([true, false, true, true], $actual);
    }
}
