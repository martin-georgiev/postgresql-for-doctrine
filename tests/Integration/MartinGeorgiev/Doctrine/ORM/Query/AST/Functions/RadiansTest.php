<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians;
use PHPUnit\Framework\Attributes\Test;

class RadiansTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RADIANS' => Radians::class,
        ];
    }

    #[Test]
    public function can_convert_degrees_to_radians(): void
    {
        $dql = 'SELECT RADIANS(180) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.141592653589793, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_convert_decimal_entity_property_to_radians(): void
    {
        $dql = 'SELECT RADIANS(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.1832595714594046, $result[0]['result'], 0.000001);
    }
}
