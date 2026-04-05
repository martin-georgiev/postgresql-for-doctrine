<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan2d;
use PHPUnit\Framework\Attributes\Test;

class Atan2dTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAN2D' => Atan2d::class,
        ];
    }

    #[Test]
    public function can_calculate_atan2d_of_literals(): void
    {
        $dql = 'SELECT ATAN2D(1.0, 1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(45.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_atan2d_with_entity_properties(): void
    {
        $dql = 'SELECT ATAN2D(n.decimal1, n.decimal2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(27.121303404159, $result[0]['result'], 0.000001);
    }
}
