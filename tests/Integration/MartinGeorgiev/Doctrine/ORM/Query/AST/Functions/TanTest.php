<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tan;
use PHPUnit\Framework\Attributes\Test;

class TanTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TAN' => Tan::class,
        ];
    }

    #[Test]
    public function can_calculate_tan_of_literal(): void
    {
        $dql = 'SELECT TAN(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_tan_with_entity_property(): void
    {
        $dql = 'SELECT TAN(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.8498999934219, $result[0]['result'], 0.000001);
    }
}
