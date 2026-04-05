<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gcd;
use PHPUnit\Framework\Attributes\Test;

class GcdTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GCD' => Gcd::class,
        ];
    }

    #[Test]
    public function can_calculate_gcd_of_literals(): void
    {
        $dql = 'SELECT GCD(12, 8) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4, $result[0]['result']);
    }

    #[Test]
    public function can_calculate_gcd_with_entity_properties(): void
    {
        $dql = 'SELECT GCD(n.integer1, n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }
}
