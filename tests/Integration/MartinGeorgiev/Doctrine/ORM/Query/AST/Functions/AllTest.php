<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All;
use PHPUnit\Framework\Attributes\Test;

class AllTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ALL_OF' => All::class,
        ];
    }

    #[Test]
    public function matches_when_value_satisfies_all_elements(): void
    {
        $dql = 'SELECT t.id as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1 AND 0 < ALL_OF(t.integerArray)';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function does_not_match_when_value_fails_for_any_element(): void
    {
        $dql = 'SELECT t.id as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1 AND 2 < ALL_OF(t.integerArray)';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
