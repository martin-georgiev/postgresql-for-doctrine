<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReverse;
use PHPUnit\Framework\Attributes\Test;

final class ArrayReverseTest extends ArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'array_reverse function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_REVERSE' => ArrayReverse::class,
        ];
    }

    #[Test]
    public function returns_the_reversed_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_REVERSE(ARR('apple', 'banana')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['banana', 'apple'], $actual);
    }

    #[Test]
    public function returns_the_reversed_array_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_REVERSE(t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['orange', 'banana', 'apple'], $actual);
    }
}
