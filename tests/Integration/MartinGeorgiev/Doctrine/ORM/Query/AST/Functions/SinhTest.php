<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sinh;
use PHPUnit\Framework\Attributes\Test;

final class SinhTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SINH' => Sinh::class,
        ];
    }

    #[Test]
    public function returns_the_hyperbolic_sine_from_a_literal(): void
    {
        $dql = 'SELECT SINH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_hyperbolic_sine_from_an_entity_field(): void
    {
        $dql = 'SELECT SINH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(18157.751323355093, $result[0]['result']);
    }
}
