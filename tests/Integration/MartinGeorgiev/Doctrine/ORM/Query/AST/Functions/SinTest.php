<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sin;
use PHPUnit\Framework\Attributes\Test;

class SinTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIN' => Sin::class,
        ];
    }

    #[Test]
    public function can_calculate_sin_of_literal(): void
    {
        $dql = 'SELECT SIN(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_sin_with_entity_property(): void
    {
        $dql = 'SELECT SIN(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(-0.87969575997167, $result[0]['result'], 0.000001);
    }
}
