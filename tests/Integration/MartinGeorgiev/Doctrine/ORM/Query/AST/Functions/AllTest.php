<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All;
use PHPUnit\Framework\Attributes\Test;

final class AllTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ALL_OF' => All::class,
        ];
    }

    #[Test]
    public function returns_whether_the_comparison_holds_for_every_element_from_an_entity_field(): void
    {
        $dql = 'SELECT t.id as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1 AND 0 < ALL_OF(t.integerArray)';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['result']);
    }
}
