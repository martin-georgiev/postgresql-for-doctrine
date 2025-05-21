<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString;

class ArrayToStringTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_TO_STRING' => ArrayToString::class];
    }

    public function test_array_to_string_with_default_delimiter(): void
    {
        $dql = 'SELECT ARRAY_TO_STRING(t.textArray, \',\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('apple,banana,orange', $result[0]['result']);
    }

    public function test_array_to_string_with_custom_delimiter(): void
    {
        $dql = 'SELECT ARRAY_TO_STRING(t.textArray, \' | \') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('apple | banana | orange', $result[0]['result']);
    }
}
