<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;
use PHPUnit\Framework\Attributes\Test;

final class ContainsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONTAINS' => Contains::class,
        ];
    }

    #[Test]
    public function returns_whether_the_array_contains_the_operand_from_an_entity_field(): void
    {
        $dql = 'SELECT CONTAINS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => ['banana']]);
        $this->assertTrue($result[0]['result']);
    }
}
