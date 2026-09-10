<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteInventoryItemType;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CompositeTypeTest extends TestCase
{
    private const INVENTORY_ITEM_TYPE_NAME = 'test_inventory_item';

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection->executeStatement(\sprintf(
            'CREATE TYPE %s.%s AS (name text, supplier_id integer, price numeric)',
            self::DATABASE_SCHEMA,
            self::INVENTORY_ITEM_TYPE_NAME
        ));
        $this->registerCompositeType(self::INVENTORY_ITEM_TYPE_NAME, ConcreteInventoryItemType::class);
    }

    /**
     * @param class-string<Type> $typeClass
     */
    private function registerCompositeType(string $typeName, string $typeClass): void
    {
        if (Type::hasType($typeName)) {
            Type::overrideType($typeName, $typeClass);
        } else {
            Type::addType($typeName, $typeClass);
        }

        $this->connection->getDatabasePlatform()->registerDoctrineTypeMapping($typeName, $typeName);
    }

    protected function getTypeName(): string
    {
        return self::INVENTORY_ITEM_TYPE_NAME;
    }

    #[Test]
    public function type_will_be_registered(): void
    {
        $typeName = $this->getTypeName();
        $this->assertTrue(Type::hasType($typeName));

        $type = Type::getType($typeName);
        $platform = $this->connection->getDatabasePlatform();

        $this->assertSame($typeName, $type->getSQLDeclaration([], $platform));

        if (\method_exists($type, 'requiresSQLCommentHint')) {
            $this->assertFalse($type->requiresSQLCommentHint($platform)); // @phpstan-ignore-line
        }
    }

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    /**
     * @param array<string, mixed> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(array $phpValue, string $postgresValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $phpValue, $typeName)
                ->executeStatement();

            $storedLiteral = $this->connection->fetchOne(\sprintf(
                'SELECT "%s"::text FROM %s.%s WHERE id = 1',
                $columnName,
                self::DATABASE_SCHEMA,
                $tableName
            ));
            $this->assertSame($postgresValue, $storedLiteral);

            $retrieved = $this->fetchConvertedValue($typeName, $tableName, $columnName);
            $this->assertRoundTrip($typeName, $phpValue, $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{phpValue: array<string, mixed>, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'plain values' => [
                'phpValue' => ['name' => 'widget', 'supplier_id' => 42, 'price' => '9.99'],
                'postgresValue' => '(widget,42,9.99)',
            ],
            'all fields null' => [
                'phpValue' => ['name' => null, 'supplier_id' => null, 'price' => null],
                'postgresValue' => '(,,)',
            ],
            'empty string then nulls' => [
                'phpValue' => ['name' => '', 'supplier_id' => null, 'price' => null],
                'postgresValue' => '("",,)',
            ],
            'value containing a comma' => [
                'phpValue' => ['name' => 'a,b', 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => '("a,b",1,1)',
            ],
            'value containing double quotes' => [
                'phpValue' => ['name' => 'say "hi"', 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => '("say ""hi""",1,1)',
            ],
            'value containing a backslash' => [
                'phpValue' => ['name' => 'back\\slash', 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => '("back\\\\slash",1,1)',
            ],
            'value containing parentheses' => [
                'phpValue' => ['name' => '(paren)', 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => '("(paren)",1,1)',
            ],
            'value with leading and trailing spaces' => [
                'phpValue' => ['name' => '  padded  ', 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => '("  padded  ",1,1)',
            ],
            'value containing a newline and a tab' => [
                'phpValue' => ['name' => "line1\nline2\tend", 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => "(\"line1\nline2\tend\",1,1)",
            ],
            'literal NULL text is not a null field' => [
                'phpValue' => ['name' => 'NULL', 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => '(NULL,1,1)',
            ],
            'every special character at once' => [
                'phpValue' => ['name' => "a,b \"q\" c\\d (e) \n f", 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => "(\"a,b \"\"q\"\" c\\\\d (e) \n f\",1,1)",
            ],
            'braces and quotes need no quoting' => [
                'phpValue' => ['name' => "a{b}c'd;e:f|g", 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => "(a{b}c'd;e:f|g,1,1)",
            ],
            'numeric scale is preserved' => [
                'phpValue' => ['name' => 'x', 'supplier_id' => 1, 'price' => '9.90'],
                'postgresValue' => '(x,1,9.90)',
            ],
        ];
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(mixed $value): void
    {
        $this->expectException(InvalidCompositeForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            'raw record literal instead of array' => ['(widget,42,9.99)'],
            'missing a declared field' => [['name' => 'widget', 'supplier_id' => 42]],
            'carrying an undeclared field' => [['name' => 'widget', 'supplier_id' => 42, 'price' => '1', 'color' => 'red']],
        ];
    }

    #[Test]
    public function rejects_record_with_a_field_count_the_php_side_does_not_declare(): void
    {
        // Simulates DB/PHP model drift: a migration added a field that getFieldTypes() does not know about
        $columnType = $this->getPostgresTypeName();
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $this->connection->executeStatement(\sprintf(
                'ALTER TYPE %s.%s ADD ATTRIBUTE weight numeric CASCADE',
                self::DATABASE_SCHEMA,
                self::INVENTORY_ITEM_TYPE_NAME
            ));
            $this->connection->executeStatement(\sprintf(
                'INSERT INTO %s.%s ("%s") VALUES (?)',
                self::DATABASE_SCHEMA,
                $tableName,
                $columnName
            ), ['(widget,42,9.99,1.25)']);

            $this->expectException(InvalidCompositeForPHPException::class);
            $this->fetchConvertedValue(self::INVENTORY_ITEM_TYPE_NAME, $tableName, $columnName);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
