<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle;
use PHPUnit\Framework\Attributes\Test;

final class ArrayShuffleTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_SHUFFLE' => ArrayShuffle::class,
        ];
    }

    #[Test]
    public function returns_the_shuffled_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_SHUFFLE(ARR('apple', 'banana')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEqualsCanonicalizing(['apple', 'banana'], $actual);
    }

    #[Test]
    public function returns_the_shuffled_array_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_SHUFFLE(t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEqualsCanonicalizing(['apple', 'banana', 'orange'], $actual);
    }
}
