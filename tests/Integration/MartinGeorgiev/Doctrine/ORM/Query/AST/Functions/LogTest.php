<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log;
use PHPUnit\Framework\Attributes\Test;

final class LogTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LOG' => Log::class,
        ];
    }

    #[Test]
    public function calculates_base_ten_logarithm_of_hundred(): void
    {
        $dql = 'SELECT LOG(10, 100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_base_ten_logarithm_of_entity_decimal_value(): void
    {
        $dql = 'SELECT LOG(10, n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.021189299069938, $result[0]['result']);
    }

    #[Test]
    public function calculates_logarithm_with_arithmetic_expressions(): void
    {
        $dql = 'SELECT LOG(n.integer1 / 2, n.integer2 * 5) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.1132827525593783, $result[0]['result']);
    }
}
