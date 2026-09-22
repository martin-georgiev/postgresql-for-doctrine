<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosh;
use PHPUnit\Framework\Attributes\Test;

final class AcoshTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ACOSH' => Acosh::class,
        ];
    }

    #[Test]
    public function returns_the_inverse_hyperbolic_cosine_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ACOSH(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_inverse_hyperbolic_cosine_from_an_entity_field(): void
    {
        $dql = 'SELECT ACOSH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.0422471120933285, $result[0]['result']);
    }
}
