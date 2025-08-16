<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class RangeTypeTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registerRangeOperatorFunctions();
        $this->createRangeOperatorsTable();
        $this->insertRangeOperatorsRows();
    }

    protected function registerRangeOperatorFunctions(): void
    {
        $this->configuration->addCustomStringFunction('CONTAINS', Contains::class);
        $this->configuration->addCustomStringFunction('IS_CONTAINED_BY', IsContainedBy::class);
        $this->configuration->addCustomStringFunction('OVERLAPS', Overlaps::class);
    }

    private function createRangeOperatorsTable(): void
    {
        $tableName = 'containsranges';

        // Ensure a clean slate
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                int4range INT4RANGE,
                int8range INT8RANGE,
                numrange NUMRANGE,
                daterange DATERANGE,
                tsrange TSRANGE,
                tstzrange TSTZRANGE
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    private function insertRangeOperatorsRows(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containsranges (
                int4range,
                int8range,
                numrange,
                daterange,
                tsrange,
                tstzrange
            ) VALUES
            (\'[1,10)\', \'[100,1000)\', \'[1.5,10.7)\', \'[2023-01-01,2023-12-31)\', \'[2023-01-01 10:00:00,2023-01-01 18:00:00)\', \'[2023-01-01 10:00:00+00,2023-01-01 18:00:00+00)\'),
            (\'[5,15)\', \'[500,1500)\', \'[5.5,15.7)\', \'[2023-06-01,2023-12-31)\', \'[2023-06-01 10:00:00,2023-06-01 18:00:00)\', \'[2023-06-01 10:00:00+00,2023-06-01 18:00:00+00)\'),
            (\'[20,30)\', \'[2000,3000)\', \'[20.5,30.7)\', \'[2023-12-01,2023-12-31)\', \'[2023-12-01 10:00:00,2023-12-01 18:00:00)\', \'[2023-12-01 10:00:00+00,2023-12-01 18:00:00+00)\')
        ', self::DATABASE_SCHEMA);

        $this->connection->executeStatement($sql);
    }

    /**
     * @param array<int, array<string, mixed>> $result
     */
    protected function assertIds(array $expectedIds, array $result): void
    {
        $this->assertCount(\count($expectedIds), $result);

        $actualIds = [];
        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('id', $row, 'Result row is expected to contain an id key');
            $this->assertIsInt($row['id']);
            $actualIds[] = $row['id'];
        }

        foreach ($expectedIds as $expectedId) {
            $this->assertContains($expectedId, $actualIds, 'Expected id not found in result set');
        }
    }

    /**
     * Override to handle Range-specific value object comparison.
     */
    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$expected instanceof RangeValueObject || !$actual instanceof RangeValueObject) {
            throw new \InvalidArgumentException('assertTypeValueEquals in RangeTypeTestCase expects RangeValueObject arguments.');
        }

        $this->assertRangeEquals($expected, $actual, $typeName);
    }

    /**
     * Assert that two range value objects are equal.
     *
     * @param RangeValueObject<\DateTimeInterface|float|int> $expected
     * @param RangeValueObject<\DateTimeInterface|float|int> $actual
     */
    protected function assertRangeEquals(RangeValueObject $expected, RangeValueObject $actual, string $typeName): void
    {
        $this->assertEquals(
            $expected->__toString(),
            $actual->__toString(),
            'Range string representation mismatch for type '.$typeName
        );

        $this->assertEquals(
            $expected->isEmpty(),
            $actual->isEmpty(),
            'Range empty state mismatch for type '.$typeName
        );
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, null);
    }

    /**
     * Data-driven test for Range value objects.
     * Subclasses should add #[DataProvider('provideValidTransformations')].
     *
     * @param RangeValueObject<\DateTimeInterface|float|int> $rangeValueObject
     */
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, $rangeValueObject);
    }

    /**
     * @param array<int> $expectedIds
     */
    #[DataProvider('provideOperatorScenarios')]
    #[Test]
    public function can_evaluate_operator_scenarios(string $name, string $dql, array $expectedIds): void
    {
        $result = $this->executeDqlQuery($dql);
        $this->assertIds($expectedIds, $result);
    }

    /**
     * @return array<string, array{string, string, array<int>}> [name, dql, expectedIds]
     */
    abstract public static function provideOperatorScenarios(): array;
}
