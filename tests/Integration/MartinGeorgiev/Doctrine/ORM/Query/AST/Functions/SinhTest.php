<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sinh;
use PHPUnit\Framework\Attributes\Test;

class SinhTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SINH' => Sinh::class,
        ];
    }

    #[Test]
    public function sinh_of_zero(): void
    {
        $dql = 'SELECT SINH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function sinh_with_entity_property(): void
    {
        $dql = 'SELECT SINH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(18157.751323355, $result[0]['result'], 0.000001);
    }
}
