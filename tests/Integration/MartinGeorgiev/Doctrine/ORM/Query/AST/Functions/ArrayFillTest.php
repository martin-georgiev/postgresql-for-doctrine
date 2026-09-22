<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill;
use PHPUnit\Framework\Attributes\Test;

final class ArrayFillTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_FILL' => ArrayFill::class,
        ];
    }

    #[Test]
    public function returns_the_filled_array_from_numeric_literals(): void
    {
        $dql = "SELECT ARRAY_FILL(7, ARR('3')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame([7, 7, 7], $actual);
    }

    #[Test]
    public function returns_the_filled_array_from_an_entity_field(): void
    {
        $dql = "SELECT ARRAY_FILL(t.id, ARR('3')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame([1, 1, 1], $actual);
    }

    #[Test]
    public function returns_the_filled_array_with_custom_lower_bounds(): void
    {
        $dql = "SELECT ARRAY_FILL(7, ARR('3'), ARR('2')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[2:4]={7,7,7}', $result[0]['result']);
    }
}
