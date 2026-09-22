<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tanh;
use PHPUnit\Framework\Attributes\Test;

final class TanhTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TANH' => Tanh::class,
        ];
    }

    #[Test]
    public function returns_the_hyperbolic_tangent_from_a_literal(): void
    {
        $dql = 'SELECT TANH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_hyperbolic_tangent_from_an_entity_field(): void
    {
        $dql = 'SELECT TANH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.9999999984834879, $result[0]['result']);
    }
}
