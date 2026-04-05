<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosd;
use PHPUnit\Framework\Attributes\Test;

class CosdTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSD' => Cosd::class,
        ];
    }

    #[Test]
    public function can_calculate_cosd_of_literal(): void
    {
        $dql = 'SELECT COSD(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_cosd_with_entity_property(): void
    {
        $dql = 'SELECT COSD(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.98325490756395, $result[0]['result'], 0.000001);
    }
}
