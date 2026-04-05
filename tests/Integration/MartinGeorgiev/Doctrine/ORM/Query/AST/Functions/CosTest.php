<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cos;
use PHPUnit\Framework\Attributes\Test;

class CosTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COS' => Cos::class,
        ];
    }

    #[Test]
    public function can_calculate_cos_of_literal(): void
    {
        $dql = 'SELECT COS(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_cos_with_entity_property(): void
    {
        $dql = 'SELECT COS(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(-0.47553692799599, $result[0]['result'], 0.000001);
    }
}
