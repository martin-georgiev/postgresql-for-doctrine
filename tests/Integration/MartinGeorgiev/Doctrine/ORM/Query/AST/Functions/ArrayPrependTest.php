<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend;
use PHPUnit\Framework\Attributes\Test;

final class ArrayPrependTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
            'ARRAY_PREPEND' => ArrayPrepend::class,
        ];
    }

    #[Test]
    public function returns_the_prepended_array_from_an_array_literal(): void
    {
        $dql = "SELECT ARRAY_PREPEND('orange', ARR('apple', 'banana')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['orange', 'apple', 'banana'], $actual);
    }

    #[Test]
    public function returns_the_prepended_array_from_an_entity_field(): void
    {
        $dql = "SELECT ARRAY_PREPEND('kiwi', t.textArray) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertSame(['kiwi', 'apple', 'banana', 'orange'], $actual);
    }
}
