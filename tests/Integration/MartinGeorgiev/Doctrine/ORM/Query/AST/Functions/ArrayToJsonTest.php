<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson;
use PHPUnit\Framework\Attributes\Test;

class ArrayToJsonTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_JSON' => ArrayToJson::class,
        ];
    }

    #[Test]
    public function can_convert_text_array_to_json(): void
    {
        $dql = 'SELECT ARRAY_TO_JSON(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('["apple","banana","orange"]', $result[0]['result']);
    }

    #[Test]
    public function can_convert_integer_array_to_json(): void
    {
        $dql = 'SELECT ARRAY_TO_JSON(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1,2,3]', $result[0]['result']);
    }

    #[Test]
    public function can_convert_boolean_array_to_json(): void
    {
        $dql = 'SELECT ARRAY_TO_JSON(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[true,false,true]', $result[0]['result']);
    }
}
