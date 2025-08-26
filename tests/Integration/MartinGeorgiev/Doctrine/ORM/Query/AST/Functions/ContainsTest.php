<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;
use PHPUnit\Framework\Attributes\Test;

class ContainsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONTAINS' => Contains::class,
        ];
    }

    #[Test]
    public function returns_true_when_value_is_contained_in_text_array(): void
    {
        $dql = 'SELECT CONTAINS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => ['banana']]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_value_is_contained_in_integer_array(): void
    {
        $dql = 'SELECT CONTAINS(t.integerArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => [2]]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_for_non_existing_elements(): void
    {
        $dql = 'SELECT CONTAINS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => ['mango']]);
        $this->assertFalse($result[0]['result']);
    }
}
