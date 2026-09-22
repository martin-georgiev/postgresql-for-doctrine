<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asinh;
use PHPUnit\Framework\Attributes\Test;

final class AsinhTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASINH' => Asinh::class,
        ];
    }

    #[Test]
    public function returns_the_inverse_hyperbolic_sine_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ASINH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_inverse_hyperbolic_sine_from_an_entity_field(): void
    {
        $dql = 'SELECT ASINH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.046782337219411, $result[0]['result']);
    }
}
