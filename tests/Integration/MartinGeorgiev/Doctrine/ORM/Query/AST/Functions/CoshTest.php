<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosh;
use PHPUnit\Framework\Attributes\Test;

final class CoshTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSH' => Cosh::class,
        ];
    }

    #[Test]
    public function returns_the_hyperbolic_cosine_from_a_numeric_literal(): void
    {
        $dql = 'SELECT COSH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_hyperbolic_cosine_from_an_entity_field(): void
    {
        $dql = 'SELECT COSH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(18157.751350891544, $result[0]['result']);
    }
}
