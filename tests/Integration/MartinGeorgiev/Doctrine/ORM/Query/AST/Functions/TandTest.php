<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tand;
use PHPUnit\Framework\Attributes\Test;

final class TandTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TAND' => Tand::class,
        ];
    }

    #[Test]
    public function calculates_tand_of_literal(): void
    {
        $dql = 'SELECT TAND(45.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_tand_with_entity_property(): void
    {
        $dql = 'SELECT TAND(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.18533904493153, $result[0]['result'], 0.00000000000001);
    }
}
