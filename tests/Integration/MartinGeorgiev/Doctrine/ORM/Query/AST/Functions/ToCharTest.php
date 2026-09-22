<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase;

final class ToCharTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestSchema();
        $this->createTestTableForDateFixture();
        $this->createTestTableForNumericFixture();
    }

    protected function getStringFunctions(): array
    {
        return [
            'to_char' => ToChar::class,
        ];
    }

    #[Test]
    public function returns_a_formatted_value_from_an_entity_field(): void
    {
        $dql = "SELECT to_char(t.datetimetz1, 'HH12:MI:SS') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('10:30:00', $result[0]['result']);
    }

    #[Test]
    public function returns_a_formatted_value_from_a_literal(): void
    {
        $dql = "SELECT to_char(125.80, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('125.80+', $result[0]['result']);
    }

    #[Test]
    public function rejects_a_null_argument(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_char(NULL, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    private function createTestTableForDateFixture(): void
    {
        $tableName = 'containsdates';

        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                date1 DATE,
                date2 DATE,
                datetime1 TIMESTAMP,
                datetime2 TIMESTAMP,
                time1 TIME,
                time2 TIME,
                datetimetz1 TIMESTAMPTZ,
                datetimetz2 TIMESTAMPTZ,
                dateinterval1 INTERVAL
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);

        $sql = \sprintf('
            INSERT INTO %s.containsdates (date1, date2, datetime1, datetime2, time1, time2, datetimetz1, datetimetz2, dateinterval1) VALUES 
            (\'2023-06-15\', \'2023-06-16\', \'2023-06-15 10:30:00\', \'2023-06-16 11:45:00\', \'10:30:00\', \'11:45:00\', \'2023-06-15 10:30:00+00\', \'2023-06-16 11:45:00+00\', \'15h 2m 12s\')
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }

    private function createTestTableForNumericFixture(): void
    {
        $tableName = 'containsnumerics';

        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                integer1 INTEGER,
                integer2 INTEGER,
                bigint1 BIGINT,
                bigint2 BIGINT,
                decimal1 DECIMAL,
                decimal2 DECIMAL
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);

        $sql = \sprintf('
            INSERT INTO %s.containsnumerics (integer1, integer2, bigint1, bigint2, decimal1, decimal2) VALUES 
            (10, 20, 1000, 2000, -125.8, 20.5)
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
