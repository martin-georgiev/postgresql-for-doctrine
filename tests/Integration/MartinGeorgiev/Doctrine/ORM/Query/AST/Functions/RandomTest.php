<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random;
use PHPUnit\Framework\Attributes\Test;

final class RandomTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANDOM' => Random::class,
        ];
    }

    #[Test]
    public function returns_a_random_value(): void
    {
        $dql = 'SELECT RANDOM() as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertGreaterThanOrEqual(0.0, $result[0]['result']);
        $this->assertLessThanOrEqual(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_a_random_integer_from_a_literal_operand(): void
    {
        $this->requirePostgresVersion(170000, 'random() with bounds');

        $dql = 'SELECT RANDOM(1, t.integer1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertGreaterThanOrEqual(1, $result[0]['result']);
        $this->assertLessThanOrEqual(10, $result[0]['result']);
    }

    #[Test]
    public function returns_a_random_integer_from_entity_fields(): void
    {
        $this->requirePostgresVersion(170000, 'random() with bounds');

        $dql = 'SELECT RANDOM(t.integer1, t.integer2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertGreaterThanOrEqual(10, $result[0]['result']);
        $this->assertLessThanOrEqual(20, $result[0]['result']);
    }
}
