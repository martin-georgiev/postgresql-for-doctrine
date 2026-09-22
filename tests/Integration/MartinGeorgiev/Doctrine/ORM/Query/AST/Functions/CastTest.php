<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase;

final class CastTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForTextFixture();
        $this->createTestTableForArrayFixture();
    }

    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
        ];
    }

    #[Test]
    public function returns_a_value_cast_to_a_bare_type_from_an_entity_field(): void
    {
        $dql = 'SELECT CAST(t.text1 AS INTEGER) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(123, $result[0]['result']);
    }

    #[Test]
    public function returns_a_value_cast_to_a_bare_type_from_a_text_literal(): void
    {
        $dql = "SELECT CAST('123' AS INTEGER) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(123, $result[0]['result']);
    }

    #[Test]
    public function returns_a_value_cast_to_a_parameterised_type_from_an_entity_field(): void
    {
        $dql = 'SELECT CAST(t.text1 AS DECIMAL(10, 2)) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('123.00', $result[0]['result']);
    }

    #[Test]
    public function returns_a_value_cast_to_an_array_type_from_an_entity_field(): void
    {
        $dql = 'SELECT CAST(a.integerArray AS TEXT[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{10,20,30}', $result[0]['result']);
    }

    #[Test]
    public function returns_a_value_cast_to_a_mixed_case_array_type_from_an_entity_field(): void
    {
        $dql = 'SELECT CAST(a.integerArray AS Text[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{10,20,30}', $result[0]['result']);
    }

    #[Test]
    public function returns_a_value_cast_to_a_parameterised_array_type_from_an_entity_field(): void
    {
        $dql = 'SELECT CAST(a.integerArray AS DECIMAL(10, 2)[]) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays a WHERE a.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{10.00,20.00,30.00}', $result[0]['result']);
    }

    #[Test]
    public function rejects_an_unknown_target_type(): void
    {
        $this->expectException(DriverException::class);
        $dql = "SELECT CAST('invalid' AS INVALID_TYPE) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function rejects_a_null_argument(): void
    {
        $this->expectException(QueryException::class);
        $dql = 'SELECT CAST(NULL AS INTEGER) AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $this->executeDqlQuery($dql);
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
}
