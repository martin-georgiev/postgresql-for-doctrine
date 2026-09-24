<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NthValueOver;
use PHPUnit\Framework\Attributes\Test;

final class NthValueOverTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NTH_VALUE_OVER' => NthValueOver::class,
        ];
    }

    #[Test]
    public function returns_the_nth_value_from_a_numeric_literal(): void
    {
        $dql = 'SELECT NTH_VALUE_OVER(5, 1, ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_nth_value_from_entity_fields(): void
    {
        $dql = 'SELECT NTH_VALUE_OVER(n.integer1, 1, PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
