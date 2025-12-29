<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any;
use PHPUnit\Framework\Attributes\Test;

class AnyTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ANY_OF' => Any::class,
        ];
    }

    #[Test]
    public function matches_when_value_equals_any_element(): void
    {
        $dql = "SELECT t.id as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1 AND 'banana' = ANY_OF(t.textArray)";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function does_not_match_when_value_not_in_array(): void
    {
        $dql = "SELECT t.id as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1 AND 'pear' = ANY_OF(t.textArray)";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
