<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat;
use PHPUnit\Framework\Attributes\Test;

final class ArrayCatTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_CAT' => ArrayCat::class,
        ];
    }

    #[Test]
    public function returns_the_concatenated_array_from_array_literals(): void
    {
        $dql = "SELECT ARRAY_CAT(ARR('apple', 'banana'), ARR('orange', 'kiwi')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'banana', 'orange', 'kiwi'], $actual);
    }

    #[Test]
    public function returns_the_concatenated_array_from_entity_fields(): void
    {
        $dql = 'SELECT ARRAY_CAT(t.textArray, t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'banana', 'orange', 'apple', 'banana', 'orange'], $actual);
    }
}
