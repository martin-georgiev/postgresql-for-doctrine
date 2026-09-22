<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;
use PHPUnit\Framework\Attributes\Test;

final class ArrayRemoveTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_REMOVE' => ArrayRemove::class,
        ];
    }

    #[Test]
    public function returns_the_reduced_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_REMOVE(ARR('apple', 'banana'), 'banana') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple'], $actual);
    }

    #[Test]
    public function returns_the_reduced_array_from_an_entity_field(): void
    {
        $dql = "SELECT ARRAY_REMOVE(t.textArray, 'banana') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'orange'], $actual);
    }
}
