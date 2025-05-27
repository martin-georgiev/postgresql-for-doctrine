<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;

class ToCharTest extends DateTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSimpleNumericTableWithDateFixture();
    }

    protected function getStringFunctions(): array
    {
        return [
            'to_char' => ToChar::class,
            'to_timestamp' => ToTimestamp::class,
        ];
    }

    public function test_tochar_for_timestamp(): void
    {
        $dql = "SELECT to_char(t.datetimetz1, 'HH12:MI:SS') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('10:30:00', $result[0]['result']);
    }

    public function test_tochar_for_interval(): void
    {
        $dql = "SELECT to_char(t.dateinterval1, 'HH24:MI:SS') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('15:02:12', $result[0]['result']);
    }

    public function test_tochar_for_numeric(): void
    {
        $dql = "SELECT to_char(t.decimal1, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('125.80-', $result[0]['result']);
    }

    public function test_tochar_for_numeric_literal(): void
    {
        $dql = "SELECT to_char(125.80, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('125.80+', $result[0]['result']);
    }

    public function test_tochar_for_numeric_literal_negative(): void
    {
        $dql = "SELECT to_char(125.80, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('125.80+', $result[0]['result']);
    }

    public function test_tochar_with_timestamp_function(): void
    {
        $dql = "SELECT to_char(to_timestamp('05 Dec 2000 at 11:55 and 32 seconds', 'DD Mon YYYY tt HH24:MI ttt SS ttttttt'), 'HH24:MI:SS') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('11:55:32', $result[0]['result']);
    }

    public function test_todate_with_invalid_input(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_date('invalid_date', 'DD Mon YYYY') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    public function test_todate_with_invalid_format(): void
    {
        $this->expectException(Exception::class);
        $dql = "SELECT to_char(t.decimal1, 'invalid_format') FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    public function test_tochar_invalid_input(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_char(NULL, '999D99S') AS result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    protected function createSimpleNumericTableWithDateFixture(): void
    {
        $tableName = 'containsnumerics';

        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf(
            '
            CREATE TABLE %s (
                id serial PRIMARY KEY,
                decimal1 DECIMAL
            )
        ',
            $fullTableName
        );

        $this->connection->executeStatement($sql);

        $sql = \sprintf(
            '
            INSERT INTO %s.containsnumerics (decimal1) VALUES 
            (-125.8)
        ',
            self::DATABASE_SCHEMA
        );
        $this->connection->executeStatement($sql);
    }
}
