<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLower;
use PHPUnit\Framework\Attributes\Test;

class ArrayLowerTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_LOWER' => ArrayLower::class,
        ];
    }

    #[Test]
    public function can_get_lower_bound_for_text_array(): void
    {
        $dql = 'SELECT ARRAY_LOWER(t.textArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_get_lower_bound_for_integer_array(): void
    {
        $dql = 'SELECT ARRAY_LOWER(t.integerArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1, $result[0]['result']);
    }
}

