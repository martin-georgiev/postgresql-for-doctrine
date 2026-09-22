<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power;
use PHPUnit\Framework\Attributes\Test;

final class PowerTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'POWER' => Power::class,
        ];
    }

    #[Test]
    public function returns_the_power_from_literals(): void
    {
        $dql = 'SELECT POWER(2, 3) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(8, $result[0]['result']);
    }

    #[Test]
    public function returns_the_power_from_entity_fields(): void
    {
        $dql = 'SELECT POWER(n.decimal1, n.decimal2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(859766721136081107225.6, $result[0]['result']);
    }

    #[Test]
    public function returns_the_power_from_arithmetic_expressions(): void
    {
        $dql = 'SELECT POWER(n.integer1 + n.integer2, n.integer1 / 5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(900, $result[0]['result']);
    }
}
