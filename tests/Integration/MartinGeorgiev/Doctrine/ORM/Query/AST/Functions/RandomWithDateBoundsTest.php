<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random;
use PHPUnit\Framework\Attributes\Test;

final class RandomWithDateBoundsTest extends DateTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(190000, 'random() with date and timestamp bounds');
    }

    protected function getStringFunctions(): array
    {
        return [
            'RANDOM' => Random::class,
        ];
    }

    #[Test]
    public function returns_a_random_date_between_entity_fields(): void
    {
        $dql = 'SELECT RANDOM(t.date1, t.date2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertContains($result[0]['result'], ['2023-06-15', '2023-06-16']);
    }

    #[Test]
    public function returns_a_random_timestamp_between_entity_fields(): void
    {
        $dql = 'SELECT RANDOM(t.datetime1, t.datetime2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertGreaterThanOrEqual('2023-06-15 10:30:00', $result[0]['result']);
        $this->assertLessThanOrEqual('2023-06-16 11:45:00', $result[0]['result']);
    }
}
