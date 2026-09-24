<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any;
use PHPUnit\Framework\Attributes\Test;

final class AnyTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ANY_OF' => Any::class,
        ];
    }

    #[Test]
    public function returns_whether_the_comparison_holds_for_any_element_from_an_entity_field(): void
    {
        $dql = "SELECT t.id as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1 AND 'banana' = ANY_OF(t.textArray)";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['result']);
    }
}
