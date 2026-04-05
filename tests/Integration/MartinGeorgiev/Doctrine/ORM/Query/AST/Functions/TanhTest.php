<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tanh;
use PHPUnit\Framework\Attributes\Test;

class TanhTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TANH' => Tanh::class,
        ];
    }

    #[Test]
    public function can_calculate_tanh_of_literal(): void
    {
        $dql = 'SELECT TANH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_tanh_with_entity_property(): void
    {
        $dql = 'SELECT TANH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.99999999848349, $result[0]['result'], 0.000001);
    }
}
