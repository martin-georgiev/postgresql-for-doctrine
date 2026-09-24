<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest;
use PHPUnit\Framework\Attributes\Test;

final class UnnestTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UNNEST' => Unnest::class,
        ];
    }

    #[Test]
    public function returns_one_row_per_element_from_an_entity_field(): void
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
}
