<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrimArray;
use PHPUnit\Framework\Attributes\Test;

final class TrimArrayTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'TRIM_ARRAY' => TrimArray::class,
        ];
    }

    #[Test]
    public function returns_the_trimmed_array_from_an_array_literal(): void
    {
        $dql = "SELECT TRIM_ARRAY(ARR('apple', 'banana', 'orange'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'banana'], $actual);
    }

    #[Test]
    public function returns_the_trimmed_array_from_an_entity_field(): void
    {
        $dql = 'SELECT TRIM_ARRAY(t.textArray, 2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['banana', 'orange'], $actual);
    }
}
