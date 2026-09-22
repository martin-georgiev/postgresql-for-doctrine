<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString;
use PHPUnit\Framework\Attributes\Test;

final class ArrayToStringTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_TO_STRING' => ArrayToString::class,
        ];
    }

    #[Test]
    public function converts_an_array_literal_to_a_delimited_string(): void
    {
        $dql = "SELECT ARRAY_TO_STRING(ARR('apple', 'banana'), ',') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('apple,banana', $result[0]['result']);
    }

    #[Test]
    public function converts_an_entity_field_to_a_delimited_string(): void
    {
        $dql = "SELECT ARRAY_TO_STRING(t.textArray, ',') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('apple,banana,orange', $result[0]['result']);
    }
}
