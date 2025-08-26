<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase;

class CastTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForTextFixture();
        $this->createTestTableForArrayFixture();
        $this->createTestTableForNumericFixture();
    }

    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
        ];
    }

    #[Test]
    public function can_convert_text_to_integer(): void
    {
        $dql = 'SELECT CAST(t.text1 AS INTEGER) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(123, $result[0]['result']);
    }

    #[Test]
    public function can_convert_text_to_text(): void
    {
        $dql = 'SELECT CAST(t.text1 AS TEXT) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('123', $result[0]['result']);
    }

    #[Test]
    public function can_convert_text_to_boolean(): void
    {
        $dql = 'SELECT CAST(t.text2 AS BOOLEAN) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function can_convert_text_to_decimal(): void
    {
        $dql = 'SELECT CAST(t.text1 AS DECIMAL(10, 2)) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('123.00', $result[0]['result']);
    }

    #[Test]
    public function can_convert_array_to_text_array(): void
    {
        $dql = 'SELECT CAST(a.integerArray AS TEXT[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('{', $result[0]['result']);
    }

    #[Test]
    public function can_convert_boolean_array_to_integer_array(): void
    {
        $dql = 'SELECT CAST(a.boolArray AS INTEGER[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('{', $result[0]['result']);
    }

    #[Test]
    public function can_use_in_where_condition(): void
    {
        $dql = 'SELECT t.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE CAST(t.text1 AS INTEGER) > 100';
        $result = $this->executeDqlQuery($dql);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function can_use_in_complex_query(): void
    {
        $dql = 'SELECT t.id, CAST(t.text1 AS INTEGER) as casted_text FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id IN (1, 2, 3)';
        $result = $this->executeDqlQuery($dql);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function can_convert_numeric_to_integer(): void
    {
        $dql = 'SELECT CAST(n.decimal1 AS INTEGER) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(11, $result[0]['result'], 'PostgreSQL is expected to round 10.5 to 11');
    }

    #[Test]
    public function can_convert_numeric_to_decimal(): void
    {
        $dql = 'SELECT CAST(n.integer1 AS DECIMAL(10, 2)) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('10.00', $result[0]['result']);
    }

    #[Test]
    public function throws_exception_for_invalid_type(): void
    {
        $this->expectException(DriverException::class);
        $dql = "SELECT CAST('invalid' AS INVALID_TYPE) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function throws_exception_for_null_input(): void
    {
        $this->expectException(QueryException::class);
        $dql = 'SELECT CAST(NULL AS INTEGER) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function can_use_lowercase_array_types(): void
    {
        $dql = 'SELECT CAST(a.integerArray AS int[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('{', $result[0]['result']);
    }

    #[Test]
    public function can_use_mixed_case_array_types(): void
    {
        $dql = 'SELECT CAST(a.integerArray AS Text[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('{', $result[0]['result']);
    }

    #[Test]
    public function can_use_parameterized_decimal_array(): void
    {
        $dql = 'SELECT CAST(a.integerArray AS DECIMAL(10, 2)[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('{', $result[0]['result']);
    }

    private function createTestTableForTextFixture(): void
    {
        $tableName = 'containstexts';

        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                text1 TEXT,
                text2 TEXT
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);

        $sql = \sprintf('
            INSERT INTO %s.containstexts (text1, text2) VALUES 
            (\'123\', \'true\'),
            (\'456\', \'false\'),
            (\'789\', \'1\')
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }

    private function createTestTableForArrayFixture(): void
    {
        $tableName = 'containsarrays';

        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                textarray TEXT[],
                smallintarray SMALLINT[],
                integerarray INTEGER[],
                bigintarray BIGINT[],
                boolarray BOOLEAN[]
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);

        $sql = \sprintf('
            INSERT INTO %s.containsarrays (textarray, smallintarray, integerarray, bigintarray, boolarray) VALUES 
            (\'{"apple", "banana", "cherry"}\', \'{1, 2, 3}\', \'{10, 20, 30}\', \'{100, 200, 300}\', \'{true, false, true}\'),
            (\'{"dog", "cat", "bird"}\', \'{4, 5, 6}\', \'{40, 50, 60}\', \'{400, 500, 600}\', \'{false, true, false}\')
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
            (10, 20, 1000, 2000, 10.5, 20.5)
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
