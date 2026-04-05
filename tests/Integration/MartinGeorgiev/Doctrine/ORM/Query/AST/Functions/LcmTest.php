<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lcm;
use PHPUnit\Framework\Attributes\Test;

class LcmTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LCM' => Lcm::class,
        ];
    }

    #[Test]
    public function can_calculate_lcm_of_literals(): void
    {
        $dql = 'SELECT LCM(12, 8) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(24, $result[0]['result']);
    }

    #[Test]
    public function can_calculate_lcm_with_entity_properties(): void
    {
        $dql = 'SELECT LCM(n.integer1, n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(20, $result[0]['result']);
    }
}
