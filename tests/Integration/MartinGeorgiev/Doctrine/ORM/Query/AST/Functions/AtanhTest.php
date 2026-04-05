<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atanh;
use PHPUnit\Framework\Attributes\Test;

class AtanhTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATANH' => Atanh::class,
        ];
    }

    #[Test]
    public function can_calculate_atanh_of_literal(): void
    {
        $dql = 'SELECT ATANH(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_atanh_with_entity_property(): void
    {
        $dql = 'SELECT ATANH(n.decimal2 / 100.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.20794636563521, $result[0]['result'], 0.000001);
    }
}
