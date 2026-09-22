<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians;
use PHPUnit\Framework\Attributes\Test;

final class RadiansTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RADIANS' => Radians::class,
        ];
    }

    #[Test]
    public function converts_degrees_to_radians_from_a_literal(): void
    {
        $dql = 'SELECT RADIANS(180) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.141592653589793, $result[0]['result']);
    }

    #[Test]
    public function converts_degrees_to_radians_from_an_entity_field(): void
    {
        $dql = 'SELECT RADIANS(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.1832595714594046, $result[0]['result']);
    }

    #[Test]
    public function converts_degrees_to_radians_from_an_arithmetic_expression(): void
    {
        $dql = 'SELECT RADIANS(n.integer1 * 18) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.141592653589793, $result[0]['result']);
    }
}
