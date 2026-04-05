<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acos;
use PHPUnit\Framework\Attributes\Test;

class AcosTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ACOS' => Acos::class,
        ];
    }

    #[Test]
    public function can_calculate_acos_of_literal(): void
    {
        $dql = 'SELECT ACOS(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_acos_with_entity_property(): void
    {
        $dql = 'SELECT ACOS(n.decimal2 / 100.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.3643326195339, $result[0]['result'], 0.000001);
    }
}
