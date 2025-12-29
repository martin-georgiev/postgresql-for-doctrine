<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest;
use PHPUnit\Framework\Attributes\Test;

class UnnestTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UNNEST' => Unnest::class,
        ];
    }

    #[Test]
    public function can_expand_array_to_rows(): void
    {
        $dql = 'SELECT UNNEST(t.textArray) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(3, $result);

        $values = \array_column($result, 'result');
        $this->assertContains('apple', $values);
        $this->assertContains('banana', $values);
        $this->assertContains('orange', $values);
    }

    #[Test]
    public function can_expand_integer_array(): void
    {
        $dql = 'SELECT UNNEST(t.integerArray) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(3, $result);

        $values = \array_column($result, 'result');
        $this->assertContains(1, $values);
        $this->assertContains(2, $values);
        $this->assertContains(3, $values);
    }
}
