<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Fixtures\MartinGeorgiev\Doctrine\Colors;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteColorType;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteShadeType;
use Fixtures\MartinGeorgiev\Doctrine\Shades;
use Fixtures\MartinGeorgiev\Doctrine\Sizes;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EnumTypeTest extends TestCase
{
    private const DBAL_TYPE_NAME = 'test_color';

    private const SHADE_TYPE_NAME = 'test_shade';

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection->executeStatement(\sprintf(
            "CREATE TYPE %s.%s AS ENUM ('red', 'blue', 'green')",
            self::DATABASE_SCHEMA,
            self::DBAL_TYPE_NAME
        ));

        if (!Type::hasType(self::DBAL_TYPE_NAME)) {
            Type::addType(self::DBAL_TYPE_NAME, ConcreteColorType::class);
        } else {
            Type::overrideType(self::DBAL_TYPE_NAME, ConcreteColorType::class);
        }

        $this->connection->getDatabasePlatform()->registerDoctrineTypeMapping(self::DBAL_TYPE_NAME, self::DBAL_TYPE_NAME);
    }

    protected function getTypeName(): string
    {
        return self::DBAL_TYPE_NAME;
    }

    /**
     * Enum types return the user-defined type name as-is (lowercase), not an uppercase SQL keyword.
     */
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
        $this->runDbalBindingRoundTrip(self::DBAL_TYPE_NAME, self::DBAL_TYPE_NAME, null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(Colors $colors): void
    {
        $this->runDbalBindingRoundTrip(self::DBAL_TYPE_NAME, self::DBAL_TYPE_NAME, $colors);
    }

    /**
     * @return array<string, array{Colors}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'red' => [Colors::RED],
            'blue' => [Colors::BLUE],
        ];
    }

    #[DataProvider('provideNonBackedEnumValues')]
    #[Test]
    public function rejects_non_backed_enum_value(mixed $value): void
    {
        $this->expectException(InvalidEnumForDatabaseException::class);

        $this->runDbalBindingRoundTrip(self::DBAL_TYPE_NAME, self::DBAL_TYPE_NAME, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideNonBackedEnumValues(): array
    {
        return [
            'raw string matching an enum case' => ['red'],
            'integer' => [42],
            'boolean' => [true],
        ];
    }

    #[Test]
    public function rejects_wrong_enum_class(): void
    {
        $this->expectException(InvalidEnumForDatabaseException::class);

        $this->runDbalBindingRoundTrip(self::DBAL_TYPE_NAME, self::DBAL_TYPE_NAME, Sizes::SMALL);
    }

    #[Test]
    public function creates_and_drops_enum_type_from_generated_ddl(): void
    {
        $concreteShadeType = new ConcreteShadeType();

        $this->connection->executeStatement($concreteShadeType->getCreateTypeSQL());

        try {
            $this->assertSame(['pale', "robin's egg"], $this->fetchEnumLabels(self::SHADE_TYPE_NAME));
        } finally {
            $this->connection->executeStatement($concreteShadeType->getDropTypeSQL());
        }

        $this->assertSame([], $this->fetchEnumLabels(self::SHADE_TYPE_NAME));
    }

    #[Test]
    public function roundtrips_value_through_enum_type_created_from_generated_ddl(): void
    {
        $concreteShadeType = new ConcreteShadeType();
        $dbalTypeName = $concreteShadeType->getName();

        if (Type::hasType($dbalTypeName)) {
            Type::overrideType($dbalTypeName, ConcreteShadeType::class);
        } else {
            Type::addType($dbalTypeName, ConcreteShadeType::class);
        }

        $this->connection->executeStatement($concreteShadeType->getCreateTypeSQL());

        $tableName = 'test_generated_enum_type';

        try {
            $this->connection->executeStatement(\sprintf(
                'CREATE TABLE %s.%s (id SERIAL PRIMARY KEY, "shade" %s)',
                self::DATABASE_SCHEMA,
                $tableName,
                $dbalTypeName
            ));

            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values(['shade' => ':value'])
                ->setParameter('value', Shades::ROBINS_EGG, $dbalTypeName)
                ->executeStatement();

            $retrieved = $this->fetchConvertedValue($dbalTypeName, $tableName, 'shade');

            $this->assertSame(Shades::ROBINS_EGG, $retrieved);
        } finally {
            // Raw SQL, because Doctrine's schema manager cannot introspect a column whose type is a user-defined enum
            $this->connection->executeStatement(\sprintf('DROP TABLE IF EXISTS %s.%s', self::DATABASE_SCHEMA, $tableName));
            $this->connection->executeStatement($concreteShadeType->getDropTypeSQL());
        }
    }

    /**
     * @return list<string>
     */
    private function fetchEnumLabels(string $typeName): array
    {
        $sql = 'SELECT e.enumlabel
                FROM pg_enum e
                JOIN pg_type t ON t.oid = e.enumtypid
                JOIN pg_namespace n ON n.oid = t.typnamespace
                WHERE n.nspname = ? AND t.typname = ?
                ORDER BY e.enumsortorder';

        $labels = [];
        foreach ($this->connection->fetchFirstColumn($sql, [self::DATABASE_SCHEMA, $typeName]) as $label) {
            $this->assertIsString($label);
            $labels[] = $label;
        }

        return $labels;
    }

    #[Test]
    public function rejects_unknown_database_value(): void
    {
        // 'green' is valid in the PostgreSQL enum but absent from the PHP Colors enum,
        // simulating a DB/PHP model drift (e.g. a migration added a case that the PHP side missed)
        [$tableName, $columnName] = $this->prepareTestTable(self::DBAL_TYPE_NAME);

        try {
            $this->connection->executeStatement(
                \sprintf('INSERT INTO %s.%s ("%s") VALUES (?)', self::DATABASE_SCHEMA, $tableName, $columnName),
                ['green']
            );

            $this->expectException(InvalidEnumForPHPException::class);
            $this->fetchConvertedValue(self::DBAL_TYPE_NAME, $tableName, $columnName);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
