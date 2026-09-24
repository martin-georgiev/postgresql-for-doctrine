<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson;
use PHPUnit\Framework\Attributes\Test;

final class ArrayToJsonTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_TO_JSON' => ArrayToJson::class,
        ];
    }

    #[Test]
    public function converts_the_array_to_json_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_TO_JSON(ARR('apple', 'banana')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('["apple","banana"]', $result[0]['result']);
    }

    #[Test]
    public function converts_the_array_to_json_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_TO_JSON(t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('["apple","banana","orange"]', $result[0]['result']);
    }
}
