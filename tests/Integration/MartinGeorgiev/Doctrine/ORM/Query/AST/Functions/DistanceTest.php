<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Distance;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase;

final class DistanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('cube');
        $this->ensurePostgresExtensionInSchema('earthdistance');

        $this->createTestTableForPointFixture();
        $this->insertTestDataForPointFixture();
    }

    protected function getStringFunctions(): array
    {
        return [
            'DISTANCE' => Distance::class,
        ];
    }

    protected function createTestTableForPointFixture(): void
    {
        $tableName = 'containspoints';

        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                point1 point,
                point2 point
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForPointFixture(): void
    {
        $sql = \sprintf("
            INSERT INTO %s.containspoints (point1, point2) VALUES
            ('(-9.1393, 38.7223)', '(-0.1276, 51.5074)'),
            ('(0, 0)', '(0, 0)')
        ", self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }

    #[Test]
    public function returns_zero_distance_for_identical_points(): void
    {
        $dql = 'SELECT DISTANCE(t.point1, t.point2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsPoints t
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_distance_between_lisbon_and_london_in_statute_miles(): void
    {
        $dql = 'SELECT DISTANCE(t.point1, t.point2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsPoints t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(984.8676868553541, $result[0]['result'], 0.0001);
    }
}
