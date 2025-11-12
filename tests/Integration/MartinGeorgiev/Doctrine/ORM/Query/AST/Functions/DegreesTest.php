<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees;
use PHPUnit\Framework\Attributes\Test;

class DegreesTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DEGREES' => Degrees::class,
        ];
    }

    #[Test]
    public function degrees(): void
    {
        $dql = 'SELECT DEGREES(3.141592653589793) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(180.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function degrees_with_entity_property(): void
    {
        $dql = 'SELECT DEGREES(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(601.6056848873644, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function degrees_with_arithmetic_expression(): void
    {
        $dql = 'SELECT DEGREES(n.decimal1 / n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(60.16056848873644, $result[0]['result'], 0.000001);
    }
}
