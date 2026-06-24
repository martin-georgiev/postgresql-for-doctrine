<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asinh;
use PHPUnit\Framework\Attributes\Test;

final class AsinhTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASINH' => Asinh::class,
        ];
    }

    #[Test]
    public function calculates_asinh_of_literal(): void
    {
        $dql = 'SELECT ASINH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_asinh_with_entity_property(): void
    {
        $dql = 'SELECT ASINH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.0467823372194, $result[0]['result'], 0.0000000000001);
    }
}
