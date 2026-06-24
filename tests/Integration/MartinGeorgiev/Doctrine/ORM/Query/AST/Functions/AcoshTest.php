<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosh;
use PHPUnit\Framework\Attributes\Test;

final class AcoshTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ACOSH' => Acosh::class,
        ];
    }

    #[Test]
    public function calculates_acosh_of_literal(): void
    {
        $dql = 'SELECT ACOSH(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_acosh_with_entity_property(): void
    {
        $dql = 'SELECT ACOSH(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.0422471120933, $result[0]['result'], 0.0000000000001);
    }
}
