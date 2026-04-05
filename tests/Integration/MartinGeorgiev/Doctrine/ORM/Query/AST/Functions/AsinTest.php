<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asin;
use PHPUnit\Framework\Attributes\Test;

class AsinTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASIN' => Asin::class,
        ];
    }

    #[Test]
    public function can_calculate_asin_of_literal(): void
    {
        $dql = 'SELECT ASIN(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.5707963, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_asin_with_entity_property(): void
    {
        $dql = 'SELECT ASIN(n.decimal2 / 100.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.20646370726099, $result[0]['result'], 0.000001);
    }
}
