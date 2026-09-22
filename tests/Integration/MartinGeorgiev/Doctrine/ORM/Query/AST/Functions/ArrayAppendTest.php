<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend;
use PHPUnit\Framework\Attributes\Test;

final class ArrayAppendTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_APPEND' => ArrayAppend::class,
        ];
    }

    #[Test]
    public function returns_the_appended_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_APPEND(ARR('apple', 'banana'), 'orange') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'banana', 'orange'], $actual);
    }

    #[Test]
    public function returns_the_appended_array_from_an_entity_field(): void
    {
        $dql = "SELECT ARRAY_APPEND(t.textArray, 'kiwi') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['apple', 'banana', 'orange', 'kiwi'], $actual);
    }
}
