<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString;
use PHPUnit\Framework\Attributes\Test;

class ArrayToStringTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_STRING' => ArrayToString::class,
        ];
    }

    #[Test]
    public function can_convert_to_string_with_comma_delimiter(): void
    {
        $dql = 'SELECT ARRAY_TO_STRING(t.textArray, \',\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('apple,banana,orange', $result[0]['result']);
    }

    #[Test]
    public function can_convert_to_string_with_custom_delimiter(): void
    {
        $dql = 'SELECT ARRAY_TO_STRING(t.textArray, \' | \') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('apple | banana | orange', $result[0]['result']);
    }
}
