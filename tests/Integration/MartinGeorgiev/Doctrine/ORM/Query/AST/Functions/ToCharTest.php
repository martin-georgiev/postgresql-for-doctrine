<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase;

class ToCharTest extends TestCase
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
            'to_timestamp' => ToTimestamp::class,
        ];
    }

    #[Test]
    public function tochar_for_timestamp(): void
    {
        $dql = "SELECT to_char(t.datetimetz1, 'HH12:MI:SS') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('10:30:00', $result[0]['result']);
    }

    #[Test]
    public function tochar_for_interval(): void
    {
        $dql = "SELECT to_char(t.dateinterval1, 'HH24:MI:SS') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('15:02:12', $result[0]['result']);
    }

    #[Test]
    public function tochar_for_numeric(): void
    {
        $dql = "SELECT to_char(t.decimal1, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('125.80-', $result[0]['result']);
    }

    #[Test]
    public function tochar_for_numeric_literal(): void
    {
        $dql = "SELECT to_char(125.80, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('125.80+', $result[0]['result']);
    }

    #[Test]
    public function tochar_for_numeric_literal_negative(): void
    {
        $dql = "SELECT to_char(-125.80, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('125.80-', $result[0]['result']);
    }

    #[Test]
    public function tochar_with_subfunction(): void
    {
        $dql = "SELECT to_char(to_timestamp('05 Dec 2000 at 11:55 and 32 seconds', 'DD Mon YYYY tt HH24:MI ttt SS ttttttt'), 'HH24:MI:SS') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('11:55:32', $result[0]['result']);
    }

    #[Test]
    public function tochar_throws_with_invalid_input_type(): void
    {
        $this->expectException(DriverException::class);
        $dql = "SELECT to_char('can only be timestamp, interval or numeric, never a string', 'DD Mon YYYY') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function tochar_throws_with_invalid_format(): void
    {
        $this->expectException(Exception::class);
        $dql = "SELECT to_char(t.decimal1, 'invalid_format') FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function tochar_throws_with_unsupported_null_input(): void
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
