<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan;
use PHPUnit\Framework\Attributes\Test;

class AtanTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAN' => Atan::class,
        ];
    }

    #[Test]
    public function can_calculate_atan_of_literal(): void
    {
        $dql = 'SELECT ATAN(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.7853981, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_atan_with_entity_property(): void
    {
        $dql = 'SELECT ATAN(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4758446204521, $result[0]['result'], 0.000001);
    }
}
