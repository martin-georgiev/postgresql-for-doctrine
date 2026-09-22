<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;
use PHPUnit\Framework\Attributes\Test;

final class ArrayReplaceTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_REPLACE' => ArrayReplace::class,
        ];
    }

    #[Test]
    public function returns_the_replaced_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_REPLACE(ARR('apple', 'banana'), 'banana', 'mango') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'mango'], $actual);
    }

    #[Test]
    public function returns_the_replaced_array_from_an_entity_field(): void
    {
        $dql = "SELECT ARRAY_REPLACE(t.textArray, 'banana', 'mango') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'mango', 'orange'], $actual);
    }
}
