<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log;
use PHPUnit\Framework\Attributes\Test;

class LogTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LOG' => Log::class,
        ];
    }

    #[Test]
    public function can_calculate_base_ten_logarithm_of_hundred(): void
    {
        $dql = 'SELECT LOG(10, 100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.0, $result[0]['result']);
    }

    #[Test]
    public function can_calculate_base_ten_logarithm_of_entity_decimal_value(): void
    {
        $dql = 'SELECT LOG(10, n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0211892990699381, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function can_calculate_logarithm_with_arithmetic_expressions(): void
    {
        $dql = 'SELECT LOG(n.integer1 / 2, n.integer2 * 5) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.861353116146, $result[0]['result'], 0.000001);
    }
}
